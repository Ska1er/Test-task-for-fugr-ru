<?php
namespace App\Controllers;


use App\Controllers\ApiController;
use App\Models\User;
use App\Services\Database\Contracts\NotebookContext as ContractNotebookContext;
use App\Services\Contracts\ImageSaver as ContractImageSaver;
use App\Services\Validator;

/**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Notebook API",
     *      description="API for Notebook",
     * )
    */
class NotebookController extends ApiController {
    private ContractNotebookContext $context;
    private Validator $validator;
    private ContractImageSaver $imageSaver;

    public function __construct(ContractNotebookContext $context, ContractImageSaver $imageSaver ,Validator $validator)
    {
        $this->context = $context;
        $this->validator = $validator;
        $this->imageSaver = $imageSaver;
    }

    /**
     * @OA\Post(path="/api/v1/notebook", tags={"Notebook"},
     * @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              required={"name", "surname", "patronymic", "phone", "email"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="surname", type="string"),
     *              @OA\Property(property="patronymic", type="string"),
     *              @OA\Property(property="phone", type="string", description="Format: +7(xxx)-xxx-xx-xx", example="+7(999)-999-99-99"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="birth", type="string"),
     *              @OA\Property(property="company", type="string"),
     *              @OA\Property(property="image", type="string", format="binary"),
     *          )
     *      )
     *  ),
     * @OA\Response(response="201", description="Created"),
     * @OA\Response(response="404", description="Not found")
     * )
     */
    public function add(array $params, $image = null)
    {
        $errors = $this->validateMandatoryFields($params);
        if(!$errors) {
            $user = new User(
                $params['name'],
                $params['surname'],
                $params['patronymic'],
                $params['phone'],
                $params['email'],
            );

            $errors = $this->validateArbitraryFields($params, $user);

            if (isset($image) && !$errors) {
                $imageName = $this->imageSaver->save($image);
    
                if ($imageName) {
                    $user->setNameOfImage($imageName);
                } else
                    $errors['error-image'] = 'The image is not in the correct format or is large size';
            }

            if (!$errors) {
                $id = $this->context->addUser($user);
    
                if ($id) {
                    return $this->simpleResponse(201, 'User was added', true, ['user_id' => $id]);
                }
                return $this->simpleResponse(403, 'Error for adding', false);
            }

        }

        return $this->simpleResponse(403, 'Error for validating', false, $errors);
    }

    private function validateMandatoryFields(array $params)
    {
        $errors = array();
        if(!isset($params['name'])){
            $errors['error-name'] = "Имя не указано";
        }

        if(!isset($params['surname'])){
            $errors['error-surname'] = "Фамилия не указана";
        }

        if(!isset($params['patronymic'])){
            $errors['error-patronymic'] = "Отчество не указано";
        }

        if(!isset($params['phone'])
        || !preg_match("@\+7\([0-9]{3}\)-[0-9]{3}-[0-9]{2}-[0-9]{2}@", $params['phone'])){
            $errors['error-phone'] = "Номер телефона не соответствует шаблону: +7(999)-999-99-99";
        }

        if(!isset($params['email']) || !filter_var($params['email'], FILTER_VALIDATE_EMAIL)){
            $errors['error-email'] = "Почта указана не верно";
        }

        return $errors;
    }

    private function validateArbitraryFields(array $params, User $user){
        $errors = array();
        if (isset($params['company'])) {
            $user->setCompany($params['company']);
        }

        if (isset($params['birth'])) {
            $birth = $this->validator->verifyDate($params['birth']);
            if ($birth) {
                $user->setBirth($birth);
            } else $errors['error-birth'] = 'Wrong date format. Date format should be Y-m-d';
        }

        return $errors;
    }

    /**
     * @OA\Delete( path="/api/v1/notebook/{id}", tags={"Notebook"},
     * @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="Id of notebook",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *      )
     * ),
     * @OA\Response(response="200", description="Success"),
     * @OA\Response(response="404", description="Not found"),
     * @OA\Response(response="500", description="Server error")
     * )
     */
    public function remove(int $id)
    {
        $user = $this->context->receiveUser($id);
        if($user){
            $image = $user->getNameOfImage();

            if($image && !$this->imageSaver->remove($image)){
                return $this->simpleResponse(500, 'Error while removing the image', false);
            }

            if($this->context->deleteUser($id)){
                return $this->simpleResponse(200, 'User was deleted');
            }
        }
        return $this->simpleResponse(404, 'User wasn\'t found', false);
    }

    /**
     * @OA\Post(path="/api/v1/notebook/{id}", tags={"Notebook"},
     * @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="Id of notebook",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *      )
     * ),
     * @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              required={"name", "surname", "patronymic", "phone", "email"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="surname", type="string"),
     *              @OA\Property(property="patronymic", type="string"),
     *              @OA\Property(property="phone", type="string", description="Format: +7(xxx)-xxx-xx-xx", example="+7(999)-999-99-99"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="birth", type="string"),
     *              @OA\Property(property="company", type="string"),
     *              @OA\Property(property="image", type="string", format="binary"),
     *          )
     *      )
     *  ),
     * @OA\Response(response="201", description="Created"),
     * @OA\Response(response="404", description="Not found")
     * )
     */
    public function update(int $id, array $params, $image = null)
    {
        $oldUser = $this->context->receiveUser($id);
        if(!$oldUser){
            return $this->simpleResponse(404, 'User wasn\'t found', false);
        }

        $errors = $this->validateMandatoryFields($params);
        if (!$errors) {

            $user= new User(
                $params['name'],
                $params['surname'],
                $params['patronymic'],
                $params['phone'],
                $params['email'],
            );

            $user->setId($id);
            $errors = $this->validateArbitraryFields($params, $user);

            if (isset($image) && !$errors) {
                $imageName = $this->imageSaver->save($image);
    
                if ($imageName) {
                    $user->setNameOfImage($imageName);
                } else
                    $errors['error-image'] = 'The image is not in the correct format or is large size';
            }

            if(!$errors){

                if($user->getNameOfImage() !== null){
                    $this->imageSaver->remove($user->getNameOfImage());
                }
                    

                $this->context->updateUser($user);

                return $this->simpleResponse(200, "User is updated", true);
            }
        }

        return $this->simpleResponse(400, "Bad request", false, $errors);
    }

    /**
     * @OA\Get( path="/api/v1/notebook/{id}", tags={"Notebook"},
     * @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="Id of notebook",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *      )
     * ),
     * @OA\Response(response="200", description="Success"),
     * @OA\Response(response="404", description="Not found")
     * )
     */
    public function getOne(int $id)
    {
        $user = $this->context->receiveUser($id);
        
        if($user)
            return $this->objectResponse(200, $user);
        else return $this->simpleResponse(404, "User wasn't found", false);
    }

    /**
     * @OA\Get( path="/api/v1/notebook/page/{page}", tags={"Notebook"},
     * @OA\Parameter(
     *      name="page",
     *      in="path",
     *      description="Number of page",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *      )
     * ),
     * @OA\Response(response="200", description="Success"),
     * @OA\Response(response="404", description="Not found")
     * )
     */
    public function getPage(int $page)
    {
        $users = $this->context->receiveUserPage($page);

        if($users) 
            return $this->objectResponse(200, $users);
        else return $this->simpleResponse(404, "Users weren't found", false);
    }

    /**
     * @OA\Get( path="/api/v1/notebook", tags={"Notebook"},
     * @OA\Response(response="200", description="Success"),
     * @OA\Response(response="404", description="Not found")
     * )
     */
    public function getAll()
    {
        $users = $this->context->receiveAllUsers();

        if($users) 
            return $this->objectResponse(200, $users);
        else return $this->simpleResponse(404, "Users weren't found", false);
    }
    
    /**
     * @OA\Get(path="/api/v1/notebook/image", tags={"Notebook"},
     * @OA\Parameter(
     *      name="image",
     *      in="query",
     *      description="Name of the image",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *      )
     * ),
     * 
     * @OA\Response(response="200", description="Success"),
     * @OA\Response(response="404", description="Not found")
     * )

    */
    public function getImage(string $name)
    {
        $image = $this->imageSaver->get($name);
        if($image){
            header('Content-type: image/jpeg');
            return $this->fileResponse(200, $image);
        }
        
        return $this->simpleResponse(404, "Image wasn't found", false);

    }
}