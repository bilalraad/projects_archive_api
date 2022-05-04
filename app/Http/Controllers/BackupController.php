<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Services\Backup;

class BackupController extends Controller
{
    protected $backup;

    public function __construct(Backup $backup)
    {
        $this->middleware('auth:sanctum');
        $this->backup = $backup;
    }

    /**
     * get all available backups
     */
    public function index()
    {
        $backups = $this->backup->getBackupList();

        foreach ($backups as $key => $backup) {
            $backups[$key]['size'] = $this->backup->sizeFormat(Backup::folderSize(base_path('backups') . DIRECTORY_SEPARATOR . $key));
        }
        return response($backups);
    }

    /**
     * create new backup with name (must send "name")
     * it also generate a date
     */
    public function store(Request $request)
    {
        $data = $this->backup->createBackupFolder($request);
        $this->backup->backupDb();
        $this->backup->backupFolder(base_path('storage/app'));

        return response('تم انشاء النسخة الاحتياطية بنجاح');
    }


    /**
     * download the sql
     * must provide the key(backup date) in this form  2022-05-04-11-11-07
     * y-m-d-h-min-sec
     */
    public function downloadDatabase($key)
    {
        $path = base_path('backups/') . $key;
        foreach ($this->backup->scanFolder($path) as $file) {
            if (strpos(basename($file), 'database') !== false) {
                return response()->download($path . DIRECTORY_SEPARATOR . $file);
            }
        }
        return true;
    }
    /**
     * download the storage files
     * must provide the key(backup date) in this form  2022-05-04-11-11-07
     */
    public function downloadStorage($key)
    {
        $path = base_path('backups/') . $key;
        foreach ($this->backup->scanFolder($path) as $file) {
            if (strpos(basename($file), 'storage') !== false) {
                return response()->download($path . DIRECTORY_SEPARATOR . $file);
            }
        }
        return true;
    }

    /**
     * restore the backup (must provide key or the actual file)
     * must provide the key(backup date) in this form  2022-05-04-11-11-07
     */
    public function restore(Request $request)
    {
        try {
            if (!is_null($request->key)) {
                $path = base_path('backups/') . $request->key;
                foreach ($this->backup->scanFolder($path) as $file) {
                    // check if it is a sql backup
                    if (strpos(basename($file), 'database') !== false) {
                        $this->backup->restoreDb($path . DIRECTORY_SEPARATOR . $file, $path);
                    }
                    // check if it is a storage backup
                    if (strpos(basename($file), 'storage') !== false) {
                        $this->backup->restore($path . DIRECTORY_SEPARATOR . $file, base_path('storage/app'));
                    }
                }
            } else if (!is_null($request->files)) {
                $data = $request->validate([
                    'file' => ["required"],
                    'file.*' => 'mimes:zip, application/octet-stream, application/x-zip-compressed, multipart/x-zip',
                ]);
                $file_name = $data['file']->getClientOriginalName();
                if (str_contains($file_name, 'database')) {
                    $this->backup->restoreDbFromFile($data['file']);
                } else if (str_contains($file_name, 'storage')) {
                    $this->backup->restore($data['file'], base_path('storage/app'));
                }
            }
            return response('تم استرجاع النسخة بنجاح');
        } catch (Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }
    /**
     * delete the backup files
     * must provide the key(backup date) in this form 2022-05-04-11-11-07
     */
    public function destroy(Request $request)
    {
        try {
            $this->backup->deleteBackup(base_path('backups/') . $request->key);
            return response('', 204);
        } catch (Exception $error) {
            return response($error->getMessage(), 500);
        }
    }
}