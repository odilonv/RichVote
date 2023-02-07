<?php
use App\Model\DataObject\Section;
/** @var Section $section */
$idQuestion = $section->getIdQuestion();
$intitule = $section -> getIntitule();
$description = $section->getDescription();
$idSection = $section->getIdSection();
if($intitule == 'intitulé')
{
    $intitule = ' ';
}
?>
<label for="<?='i'.$idSection?>"></label>
<input type="text" size="50"  id=<?='i'.$idSection?> name=<?='intitule['.$idSection.']'?> value="<?=$intitule?>">
<div class="descP"></div><h3>Description : </h3>
<?php
if($description == 'description' || $description == 'Description')
{
    $description = ' ';
}
?>
<label for="mytextarea"></label>
<textarea rows="4" cols="100" id="mytextarea" name=<?='description['.$idSection.']'?>> <?=$description?> </textarea>