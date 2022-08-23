<?php

namespace App\Models;

use DateTime;
use JsonSerializable;

class User implements JsonSerializable{
    /**
     * Fields
     */
    private int $id;
    private string $name, $surname, $patronymic;
    private string $phone, $email;
    private ?string $company, $image;
    private ?DateTime $birth;

    /**
     * Public construct
     * 
     * @param string $name
     * @param string $surname
     * @param string $patronymic
     * @param string $phone
     * @param string $email
     */
    public function __construct(string $name, string $surname, string $patronymic,
                                string $phone, string $email)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->patronymic = $patronymic;
        $this->phone = $phone;
        $this->email = $email;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
 
    public function setSurname(string $surname)
    {
        $this->surname = $surname;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function setPatronymic(string $patronymic)
    {
        $this->patronymic = $patronymic;
    }

    public function getPatronymic()
    {
        return $this->patronymic;
    }

    public function setPhone(string $phone)
    {
        $this->phone = $phone;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setCompany(?string $company)
    {
        $this->company = $company;
    }

    public function getCompany()
    {
        if(isset($this->company))
            return $this->company;
        else return null;
    }

    public function setBirth(?DateTime $birth)
    {
        $this->birth = $birth;
    }

    public function getBirth(): ?string
    {
        if(isset($this->birth))
            return $this->birth->format('Y-m-d');
        else return null;
        
    }

    public function setNameOfImage(?string $image)
    {
        $this->image = $image;
    }

    public function getNameOfImage()
    {
        if(isset($this->image))
            return $this->image;
        else return null;
    }

    /**
     * Getting all fields into an associative array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'surname' => $this->getSurname(),
            'patronymic' => $this->getPatronymic(),
            'company' => $this->getCompany(),
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
            'image' => $this->getNameOfImage(),
            'birth' => $this->getBirth(),
        ];
    }
}
