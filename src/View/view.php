<?php
use App\Lib\ConnexionUtilisateur;
?>

<!DOCTYPE html>
<html lang="fr" >
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="icon" href="../assets/img/favicon.ico" />

        <title><?php
            /** @var $pagetitle string */

            use App\Lib\MessageFlash;
            echo $pagetitle;
            ?>
        </title>
        <script src="https://cdn.tiny.cloud/1/6nd2ree755t9rejew5p12fvaqm8zz7co67iav3o6uqw4vbem/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

        <script>
            tinymce.init({
                selector: '#mytextarea',
                plugins: [
                    'advlist','autolink',
                    'lists','link','image','charmap','preview','anchor','searchreplace','visualblocks','fullscreen','insertdatetime','media','table','help','wordcount'
                ],
                toolbar: 'undo redo | formatpainter casechange blocks | bold italic backcolor | ' +
                    'alignleft aligncenter alignright alignjustify | ' +
                    'bullist numlist checklist outdent indent | removeformat | a11ycheck code table help'
            });
        </script>



    </head>

    <body>

            <nav>
                <div class="navBar">

                <a href="frontController.php?controller=user&action=accueil"><img src="../assets/img/logo.png" alt="RichVote" id="logo"></a>

                <ul>
                    <li><a href="frontController.php?controller=question&action=readAll">Questions</a></li>
                    <li><a href="frontController.php?controller=question&action=readAllArchives">Archives</a></li>
                    <li><a href="frontController.php?controller=user&action=readAll">Contributeurs</a></li>
                </ul>


                    <?php
                    if((new ConnexionUtilisateur())->estConnecte()) {
                        echo  '<div class="ligneAlign"><a id="btn-connexion" href="frontController.php?controller=user&action=read&id='.(new ConnexionUtilisateur())->getLoginUtilisateurConnecte() .'">'. (new ConnexionUtilisateur())->getLoginUtilisateurConnecte().' </a>
                                <a id="btn-connexion" href="frontController.php?controller=user&action=deconnexion">Déconnexion </a></div>';

                    }
                    else {echo  '<a id="btn-connexion" href="frontController.php?controller=user&action=connexion">Connexion </a>';}
                    ?>

                    <div class="btn">
                    <div class="line"></div>
                    <div class="line"></div>
                    <div class="line"></div>
                    </div>

                </div>
            </nav>
        <main>

            <?php


            foreach(['danger', 'warning', 'info', 'success'] as $categorie){
                if(MessageFlash::contientMessage($categorie)){
                    foreach(MessageFlash::lireMessages($categorie) as $message){
                        echo '<div class="ligneCent"><div class="alert alert-' .$categorie.'">' . $message . '</div></div>';
                    }
                }
            }

            /** @var $cheminVueBody string */
            require __DIR__ . "/{$cheminVueBody}";
            ?>
            <script>
                const menuHamburger = document.querySelector(".btn")
                const navLinks = document.querySelector(".navBar ul")

                menuHamburger.addEventListener('click',()=>{
                    navLinks.classList.toggle('mobile-menu')
                })
            </script>
        </main>
        <footer>
            <div class="vagues">
                <div class="vague" id="vague1"></div>
                <div class="vague" id="vague2"></div>
                <div class="vague" id="vague3"></div>
                <div class="vague" id="vague4"></div>

            </div>
            <ul>
                <li><a href="frontController.php?controller=user&action=accueil">Accueil</a></li>
                <li><a href="frontController.php?controller=question&action=readAll">Questions</a></li>
                <li><a href="frontController.php?controller=question&action=readAllArchives">Archives</a></li>
                <li><a href="frontController.php?controller=user&action=readAll">Contributeurs</a></li>
            </ul>

            <p>Copyright &copy; RichVote | Tous droits réservés</p>
        </footer>
    </body>
</html>