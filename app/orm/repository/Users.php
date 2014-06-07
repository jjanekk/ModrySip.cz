<?php
namespace Orm\Repository;

use Nette\Database\Context as NdbContext;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\IIdentity;
use Nette;
use Nette\Utils\Strings;
use Orm\Entity\User;
use YetORM\Entity;

class Users extends Repository implements IAuthenticator{

    protected $table = 'user';
    protected $entity = 'Orm\Entity\User';
    public static $roles = array('admin' => "Admin - veškerá práva", 'maker' => 'Tvůrce - vytváří a edituje akce a novinky', 'editor' => 'Editor - má povolení editovat akce a novinky');

    const
        COLUMN_PASSWORD = 'password',
        PASSWORD_MAX_LENGTH = 100;

    function __construct(NdbContext $context)
    {
        parent::__construct($context);
        //$this->addRole('host');
    }

    function getAllHeaders(){
        return $this->createCollection($this->getTable()->where(':user_has_functions.user_function_id IS NOT NULL')->where(':user_has_functions.user_function.main', 1));
    }

    function getAllTroopHeaders($troop_id){
        return $this->createCollection($this->getTable()->where(':user_has_functions.user_function_id IS NOT NULL')->where(':user_has_functions.user_function.troop', 1)->where('scout_troop_id', $troop_id));
    }

    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;
        $userRow = $row = null;
        $userRow = $this->getTable()->where('email = ? OR login = ? OR nick = ?', $username, $username, $username)->fetch();

        if($userRow){ $row = new User($userRow); }else{ $row = NULL; }

        if (!$row) {
            throw new Nette\Security\AuthenticationException('Toto přihlašovací jméno neexistuje..', self::IDENTITY_NOT_FOUND);

        } elseif (!self::verifyPassword($password, $row->password)) {
            throw new Nette\Security\AuthenticationException('Heslo není dobře zadnáno.', self::INVALID_CREDENTIAL);

        } elseif (PHP_VERSION_ID >= 50307 && substr($row->password, 0, 3) === '$2a') {
            $row->update(array(
                self::COLUMN_PASSWORD => self::hashPassword($password),
            ));
        }elseif(!$row->active){
            throw new Nette\Security\AuthenticationException('Váš uživatelský profil není aktivní, kontaktujte správce.', self::INVALID_CREDENTIAL);
        }

        $arr = $row->toArray();
        unset($arr[self::COLUMN_PASSWORD]);
        return new Nette\Security\Identity($row->id, $row->role, $arr);
    }

    public static function verifyPassword($password, $hash)
    {
        return self::hashPassword($password, $hash) === $hash
        || (PHP_VERSION_ID >= 50307 && substr($hash, 0, 3) === '$2a' && self::hashPassword($password, $tmp = '$2x' . substr($hash, 3)) === $tmp);
    }

    public static function hashPassword($password, $options = NULL)
    {
        if ($password === Strings::upper($password)) { // perhaps caps lock is on
            $password = Strings::lower($password);
        }
        $password = substr($password, 0, self::PASSWORD_MAX_LENGTH);
        $options = $options ?: implode('$', array(
            'algo' => PHP_VERSION_ID < 50307 ? '$2a' : '$2y', // blowfish
            'cost' => '07',
            'salt' => Strings::random(22),
        ));
        return crypt($password, $options);
    }

    function persist(Entity $ac)
    {
        $this->begin();

        $cnt = parent::persist($ac);

        // persist tags
        if (count($ac->getAddedFunctions()) || count($ac->getRemovedFunctions())) {
            
            foreach ($ac->getAddedFunctions() as $a) {
                $data = array(
                    'user_id' => $ac->id,
                    'user_function_id' => $a->id,
                );

                if ($this->getTable('user_has_functions')->where($data)->count() == 0 )
                    $this->getTable('user_has_functions')->insert($data);
            }

            $toDelete = array();
            foreach ($ac->getRemovedFunctions() as $a) {
                $toDelete[] = $a->id;
            }

            if (count($toDelete)) {
                $this->getTable('user_has_functions')
                    ->where('user_id', $ac->id)
                    ->where('user_function_id', $toDelete)
                    ->delete();
            }
        }

        $this->commit();

        return $cnt;
    }
}