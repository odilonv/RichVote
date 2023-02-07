<?php
use App\Model\DataObject\Proposition;
use App\Model\Repository\SectionRepository;

/** @var Proposition $proposition */
$intitule = $proposition->getIntitule();
if($intitule = null)
{
    $intitule = " ";
}
?>

<div class="block">
    <div class="text-box">
        <form method="post" action="frontController.php?controller=proposition&action=updated&id=<?=$proposition->getId()?>">
            <fieldset>
                <h1>Votre Proposition</h1>
                <div class="ligneCent"><div class="ligne"></div></div>
                <div class="descG"></div>


                <h3><label for="int">Proposition :</label></h3>
                <input type="text" name="intitule" id="int" size="50" value='<?=$intitule?>' >
                <br><br>



                <?php
                $i=0;
                $sectionsText = $proposition->getSectionsTexte();
                foreach ($sectionsText as $infos){
                    $idSection = $infos['section']->getId();
                    $text = $infos['texte'];
                    $i++;
                    echo '<br><div class="ligneExt" id="section'. $i .'"><h3 id="sections">'. $i .'. ' . (new SectionRepository())->select($idSection)->getIntitule() . "</h3></div>";
                    echo "<div class='ligne'></div>";
                    if($text == 'à remplir')
                    {
                        $text = ' ';
                    }
                    echo ' <div class="descP"></div><h3>Description : </h3>
                        <textarea rows="4" cols="80" maxlength="1000" id="mytextarea" name=texte[' . $idSection . '] >' . $text . '</textarea><br></form>';
                }
                ?>
            <div class="descG"></div>

                <div class="ligneCent"> <input class="optQuestion" type="submit" value="sauvegarder"/></div>
            </fieldset>
        </form>
    </div>
</div>