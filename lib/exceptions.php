<?php 

class UploadException extends Exception { 
    public function __construct($code) { 
        $message = $this->codeToMessage($code); 
        parent::__construct($message, $code); 
    } 

    private function codeToMessage($code) 
    { 
        switch ($code) { 
            case UPLOAD_ERR_INI_SIZE: 
                $message = "Размер принятого файла превысил максимально допустимый размер, который задан директивой upload_max_filesize конфигурационного файла php.ini."; 
                break; 
            case UPLOAD_ERR_FORM_SIZE: 
                $message = "Размер загружаемого файла превысил значение MAX_FILE_SIZE, указанное в HTML-форме.";
                break; 
            case UPLOAD_ERR_PARTIAL: 
                $message = "Загружаемый файл был получен только частично."; 
                break; 
            case UPLOAD_ERR_NO_FILE: 
                $message = "Файл не был загружен."; 
                break; 
            case UPLOAD_ERR_NO_TMP_DIR: 
                $message = "Отсутствует временная папка."; 
                break; 
            case UPLOAD_ERR_CANT_WRITE: 
                $message = "Не удалось записать файл на диск. "; 
                break; 
            case UPLOAD_ERR_EXTENSION: 
                $message = "PHP-расширение остановило загрузку файла. "; 
                break; 
            default: 
                $message = "Неизвестная ошибка загрузки."; 
                break; 
        } 
        return $message; 
    } 
}

// Use 
if ($_FILES['file']['error'] === "UPLOAD_ERR_OK") { 
	exit;
}
 else { 
	throw new UploadException($_FILES['file']['error']); 
}	
?>
