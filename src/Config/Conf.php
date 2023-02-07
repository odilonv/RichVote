<?php
namespace App\Config;
class Conf {
    static private array $databases = array(
        // Le nom d'hote est webinfo a l'IUT
        // ou localhost sur votre machine
        //
        // ou webinfo.iutmontp.univ-montp2.fr
        // pour accéder à webinfo depuis l'extérieur
        'hostname' => 'webinfo.iutmontp.univ-montp2.fr',
        // A l'IUT, vous avez une BDD nommee comme votre login
        // Sur votre machine, vous devrez creer une BDD
        'database' => 'souvignetn',
        // A l'IUT, c'est votre login
        // Sur votre machine, vous avez surement un compte 'root'
        'login' => 'souvignetn',
        // A l'IUT, c'est votre mdp (INE par defaut)
        // Sur votre machine personelle, vous avez creez ce mdp a l'installation
        'password' => '060781121EK'
    );

    static public function getAbsoluteUrl(): string{
        return 'https://webinfo.iutmontp.univ-montp2.fr/~esclapezl/sae-website/web/frontController.php';
    }

    static public function getLogin() : string {
        // L'attribut statique $databases s'obtient avec la syntaxe static::$databases
        // au lieu de $this->databases pour un attribut non statique
        return static::$databases['login'];
    }

    static public function getHostname() : string {
        return static::$databases['hostname'];
    }

    static public function getDatabase(){
        return static::$databases['database'];
    }

    static public function getPassword(){
        return static::$databases['password'];
    }

    static public function getDureeExpiration(): int{
        return 3600;
    }
}
?>