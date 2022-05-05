<?php

namespace App\Services;

use Carbon\Carbon;
use DB;
use Exception;
use File;
use ZipArchive;
use App\Libraries\PclZip as Zip;
use Illuminate\Filesystem\Filesystem;

class Backup
{

    protected $file;

    protected $folder;

    public function __construct()
    {
        $this->file = new Filesystem();
    }

    public function createBackupFolder($request)
    {
        $backupFolder = $this->createFolder(base_path('backups'));
        $now = Carbon::now()->format('Y-m-d-h-i-s');
        $this->folder = $this->createFolder($backupFolder . DIRECTORY_SEPARATOR . $now);

        $file = base_path('backups/backup.json');
        $data = $this->getBackupList();

        $data[$now] = [
            'name' => $request->name ?? $now,
            'date' => Carbon::now()->toDateTimeString()
        ];

        $this->saveFileData($file, $data);
        return $data;
    }

    public function backupDb()
    {
        $file = 'database-' . Carbon::now()->format('Y-m-d-h-i-s');
        $path = $this->folder . DIRECTORY_SEPARATOR . $file;

        $sql = 'mysqldump --column-statistics=0 --user=' . env('DB_USERNAME') . ' --password=' . env('DB_PASSWORD') . ' --host=' . env('DB_HOST') . ' ' . ' --port=' . env('DB_PORT') . ' ' . env('DB_DATABASE') . ' > ' . $path . '.sql';
        system($sql);
        $this->compressFileToZip($path, $file);
        if (file_exists($path . '.zip')) {
            chmod($path . '.zip', 0777);
        }
        return true;
    }

    public function restoreDb($file, $path)
    {
        $this->restore($file, $path); //extract file
        $file = $path . DIRECTORY_SEPARATOR . File::name($file) . '.sql';

        if (!file_exists($file)) {
            return false;
        }

        DB::unprepared('USE `' . env('DB_DATABASE') . '`');
        DB::connection()->setDatabaseName(env('DB_DATABASE'));
        DB::unprepared(file_get_contents($file));

        $this->deleteFile($file);
        return true;
    }

    public function restoreDbFromFile($file)
    {
        $path = base_path('backups/temp');
        $this->restore($file, $path); //extract file
        $fileName = str_replace(".zip", "", $file->getClientOriginalName());
        $file = $path . DIRECTORY_SEPARATOR . $fileName . '.sql';
        if (!file_exists($file)) {
            return false;
        }
        DB::unprepared('USE `' . env('DB_DATABASE') . '`');
        DB::connection()->setDatabaseName(env('DB_DATABASE'));
        DB::unprepared(file_get_contents($file));

        $this->deleteFile($file);
        return true;
    }

    public function backupFolder($source)
    {
        $file = $this->folder . DIRECTORY_SEPARATOR . 'storage-' . Carbon::now()->format('Y-m-d-h-i-s') . '.zip';

        // set script timeout value
        ini_set('max_execution_time', 5000);

        if (class_exists('ZipArchive', false)) {
            $zip = new ZipArchive();
            // create and open the archive
            if ($zip->open($file, ZipArchive::CREATE) !== true) {
                $this->deleteFolderBackup($this->folder);
            }
        } else {
            $zip = new Zip($file);
        }
        $arr_src = explode(DIRECTORY_SEPARATOR, $source);
        $path_length = strlen(implode(DIRECTORY_SEPARATOR, $arr_src) . DIRECTORY_SEPARATOR);
        // add each file in the file list to the archive
        $this->recurseZip($source, $zip, $path_length);
        if (class_exists('ZipArchive', false)) {
            $zip->close();
        }
        if (file_exists($file)) {
            chmod($file, 0777);
        }
        return true;
    }

    public function restore($fileName, $pathTo)
    {
        if (class_exists('ZipArchive', false)) {
            $zip = new ZipArchive;
            if ($zip->open($fileName) === true) {
                $zip->extractTo($pathTo);
                $zip->close();
                return true;
            }
        } else {
            $archive = new Zip($fileName);
            $archive->extract(PCLZIP_OPT_PATH, $pathTo, PCLZIP_OPT_REMOVE_ALL_PATH);
            return true;
        }

        return false;
    }

    public function deleteBackup($path)
    {
        foreach ($this->scanFolder($path) as $item) {
            $this->file->delete($path . DIRECTORY_SEPARATOR . $item);
        }
        $this->file->deleteDirectory($path);
        $file = base_path('backups/backup.json');
        $data = $this->getBackupList();
        if (!empty($data)) {
            $tmp = explode('/', $path);
            unset($data[end($tmp)]);
            $this->saveFileData($file, $data);
        }
    }

    public function createFolder($folder)
    {
        if (!$this->file->isDirectory($folder)) {
            $this->file->makeDirectory($folder);
            chmod($folder, 0777);
        }
        return $folder;
    }

    public function deleteFile($file)
    {
        if ($this->file->exists($file)) {
            $this->file->delete($file);
        }
    }

    public function getBackupList()
    {
        $file = base_path('backups/backup.json');
        if (file_exists($file)) {
            return $this->getFileData($file);
        }
        return [];
    }

    public function compressFileToZip($path, $name)
    {
        $filename = $path . '.zip';

        if (class_exists('ZipArchive', false)) {
            $zip = new ZipArchive();
            if ($zip->open($filename, ZipArchive::CREATE) == true) {
                $zip->addFile($path . '.sql', $name . '.sql');
                $zip->close();
            }
        } else {
            $archive = new Zip($filename);
            $archive->add($path . '.sql', PCLZIP_OPT_REMOVE_PATH, $filename);
        }
        $this->deleteFile($path . '.sql');
    }

    function saveFileData($path, $data, $json = true)
    {
        try {
            if ($json) {
                $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }
            File::put($path, $data);
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    function getFileData($file, $convert_to_array = true)
    {
        $file = File::get($file);
        if (!empty($file)) {
            if ($convert_to_array) {
                return json_decode($file, true);
            } else {
                return $file;
            }
        }
        return false;
    }

    public function recurseZip($src, &$zip, $pathLength)
    {
        foreach ($this->scanFolder($src) as $file) {
            if ($this->file->isDirectory($src . DIRECTORY_SEPARATOR . $file)) {
                $this->recurseZip($src . DIRECTORY_SEPARATOR . $file, $zip, $pathLength);
            } else {
                if (class_exists('ZipArchive', false)) {
                    $zip->addFile($src . DIRECTORY_SEPARATOR . $file, substr($src . DIRECTORY_SEPARATOR . $file, $pathLength));
                } else {
                    $zip->add($src . DIRECTORY_SEPARATOR . $file, PCLZIP_OPT_REMOVE_PATH, substr($src . DIRECTORY_SEPARATOR . $file, $pathLength));
                }
            }
        }
    }

    function scanFolder($path, $ignore_files = [])
    {
        try {
            if (is_dir($path)) {
                $data = array_diff(scandir($path), array_merge(['.', '..'], $ignore_files));
                natsort($data);
                return $data;
            }
            return [];
        } catch (Exception $ex) {
            return [];
        }
    }

    public static function folderSize($dir)
    {
        $size = 0;

        foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : folderSize($each);
        }

        return ($size);
    }

    public static function sizeFormat($bytes, $precision = 2)
    {

        $base = log($bytes, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
}