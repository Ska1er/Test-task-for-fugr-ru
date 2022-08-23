<?php
namespace App\Services\Database\Factories;

use App\Services\Database\Contracts\NotebookContext as ContractNotebookContext;
use App\Services\Database\NotebookContext;
use App\Services\ConfigReceiver;
use PDO, PDOException;

class NotebookContextFactory{
    private static ContractNotebookContext $context;

    public function create(): ContractNotebookContext{
        if(!isset(self::$context)){
            $dbConfig = ConfigReceiver::getDbConfig();
            try{
                $pdo = new PDO($dbConfig->driver, $dbConfig->user, $dbConfig->password, array(
                    PDO::ATTR_PERSISTENT => true,
                ));
                self::$context = new NotebookContext($pdo);
            }
            catch(PDOException $e){
                echo "Error!: " . $e->getMessage() . "<br/>";
                die();
            }
        }

        return self::$context;
    }
}