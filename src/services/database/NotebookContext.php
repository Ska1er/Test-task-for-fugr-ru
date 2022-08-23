<?php

namespace App\Services\Database;

use App\Models\User;
use App\Services\Database\Contracts\NotebookContext as ContractsNotebookContext;
use PDO, DateTime;

class NotebookContext extends Context implements ContractsNotebookContext {

    private const countPerPage = 5;
    
    public function addUser(User $user): int
    {   
        $sql = <<<END
                INSERT INTO users (name, surname, patronymic, phone, email, company, birth, image)
                VALUES (:name, :surname, :patronymic, :phone, :email, :company, :birth, :image)
            END;

        $attributes = [
            ':name' => $user->getName(),
            ':surname' => $user->getSurname(),
            ':patronymic' => $user->getPatronymic(),
            ':phone' => $user->getPhone(),
            ':email' => $user->getEmail(),
            ':company' => $user->getCompany(),
            ':birth' => $user->getBirth(),
            ':image' => $user->getNameOfImage(),
        ];
        $prepared = $this->connection->prepare($sql);
        $prepared->execute($attributes);
        $id = $this->connection->lastInsertId();
        return $id;
    }

    public function deleteUser(int $id): bool
    {
        $sql = "DELETE FROM users WHERE id = :id";

        $prepared = $this->connection->prepare($sql);
        $prepared->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $prepared->execute();
    }

    public function updateUser(User $user)
    {
        $sql = <<<END
            UPDATE users SET
                name = :name,
                surname = :surname,
                patronymic = :patronymic,
                company = :company,
                image = :image,
                phone = :phone,
                email = :email,
                birth = :birth
            WHERE id = :id
        END;

        $attributes = $user->jsonSerialize();
        $this->connection->prepare($sql)->execute($attributes);
    }

    public function receiveUser(int $id): User|bool
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $prepared = $this->connection->prepare($sql);

        $prepared->bindValue(':id', $id, PDO::PARAM_INT);
        $prepared->execute();

        $result = $prepared->fetch(PDO::FETCH_ASSOC);

        if($result)
            return $this->transformToUser($result);
        else return false;
    }

    public function receiveUserPage(int $page): array
    {
        $sql = 'SELECT * FROM users LIMIT :count OFFSET :offset';

        $prepared = $this->connection->prepare($sql);
        $prepared->bindValue(':count', self::countPerPage, PDO::PARAM_INT); 
        $prepared->bindValue(':offset', (($page-1) * self::countPerPage), PDO::PARAM_INT); 
        $prepared->execute();
            
        $notebooks = array();
    
        while ($row = $prepared->fetch(PDO::FETCH_ASSOC)) {
            $notebooks[] = $this->transformToUser($row);
        }
    
        return $notebooks;
    }

    public function receiveAllUsers():array
    {
        $sql = "SELECT * FROM users";

        $result = $this->connection->query($sql);

        $users = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $this->transformToUser($row);
        }

        return $users;
    }

    private function transformToUser(array $nb) : User
    {
        $user = new User(
            $nb['name'], 
            $nb['surname'], 
            $nb['patronymic'],
            $nb['phone'],
            $nb['email']
        );
        
        $user->setId($nb['id']);

        if(isset($nb['company']))
            $user->setCompany($nb['company']);

        if(isset($nb['image']))
            $user->setNameOfImage($nb['image']);

        if(isset($nb['birth']))
            $user->setBirth(DateTime::createFromFormat('Y-m-d', $nb["birth"]));

        return $user;
    }

}
