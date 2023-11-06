<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
$tempArray = Array();
$tempSortArray = Array();
foreach($arResult["SECTIONS"] as $key => $value):
	if($value["DEPTH_LEVEL"]==1)
	{
		$parent_level_1 = $value["ID"];
		$tempArray[$value["ID"]]["KEY"] =  $key;
		$tempSortArray[$value["ID"]]["KEY"] =  $key;
	}
	if($value["DEPTH_LEVEL"]==2)
	{
		$parent_level_2 = $value["ID"];
		
		
		$SectList = CIBlockSection::GetList(
			Array("sort"=>"asc"), 
			Array(
				"IBLOCK_ID"=>8, 
				"ACTIVE"=>"Y", 
				'LEFT_MARGIN' => $value["LEFT_MARGIN"], 
				'RIGHT_MARGIN' => $value["RIGHT_MARGIN"]) ,
			false, 
			array("ID"));
		$countSubsections = $SectList->SelectedRowsCount();
		
		$tempArray[$parent_level_1]["SECTIONS"][$value["ID"]]["KEY"] = $key;
		$tempArray[$parent_level_1]["SECTIONS"][$value["ID"]]["SUBSECTION_COUNT"] = $countSubsections;
		$tempSortArray[$parent_level_1]["SECTIONS"][$value["ID"]] = $countSubsections;
	}		
	if($value["DEPTH_LEVEL"]==3)
	{
		$parent_level_3 = $value["ID"];
		$tempArray[$parent_level_1]["SECTIONS"][$parent_level_2]["SECTIONS"][$value["ID"]]["KEY"] = $key;
	} 
	if($value["DEPTH_LEVEL"]==4)
	{
		$parent_level_4 = $value["ID"];
		$tempArray[$parent_level_1]["SECTIONS"][$parent_level_2]["SECTIONS"][$parent_level_3]["SECTIONS"][$value["ID"]]["KEY"] = $key;
	} 
endforeach;
// ppr($tempArray);

foreach($tempSortArray as $key => $value):
	arsort($value["SECTIONS"]);
	$tempSortArray[$key]["SECTIONS"] = $value["SECTIONS"];
endforeach;

foreach($tempSortArray as $key => $value):
	$tempSortArray[$key]["SECTIONS"] = $value["SECTIONS"];
	foreach($value["SECTIONS"] as $subkey => $subvalue):
		$tempSortArray[$key]["SECTIONS"][$subkey] = $tempArray[$key]["SECTIONS"][$subkey];
	endforeach;
endforeach;

$sectionsResultArray = Array();
foreach($tempSortArray as $key1 => $value1):
	$sectionsResultArray[] = $arResult["SECTIONS"][$value1["KEY"]];
	foreach($value1["SECTIONS"] as $key2 => $value2):
		$sectionsResultArray[] = $arResult["SECTIONS"][$value2["KEY"]];
		foreach($value2["SECTIONS"] as $key3 => $value3):
			$sectionsResultArray[] = $arResult["SECTIONS"][$value3["KEY"]];
			foreach($value3["SECTIONS"] as $key4 => $value4):
				$sectionsResultArray[] = $arResult["SECTIONS"][$value4["KEY"]];
			endforeach;
		endforeach;
	endforeach;
endforeach;
// ppr($sectionsResultArray);
$arResult["SECTIONS"] = $sectionsResultArray;
// ppr($tempSortArray);
// ppr($tempArray);
// ppr($arResult);
?>

