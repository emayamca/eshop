<?php
    ini_set("memory_limit","1000M");
    require_once "app/Mage.php";
    umask(0);
    Mage::app();
    $category = Mage::getModel ( 'catalog/category' );
    $tree = $category->getTreeModel ();
    $tree->load();
    $ids = $tree->getCollection()->getAllIds();
    if ($ids) {
    $string='';
    $heading = '"store","categories","cat_id","is_active","meta_title","meta_keywords","meta_description","include_in_menu","is_anchor","description",';
    foreach ($ids as $id) {
    if($id>0)//start if removeroot category and default category .
    {

    $cate_cre = Mage::getModel('catalog/category');

    $cate_cre->load($id);
    $treeurl='';
    $cate_cre1=Mage::getModel('catalog/category')->load($id);
    $treeurl=$cate_cre->getName();
    if($cate_cre1->getParentId()>0)
    {
    for($i=0; ;$i++)
    {
    if($cate_cre1->getParentId()>0)
    {
    $abc=Mage::getModel('catalog/category')->load($cate_cre1->getParentId());
    $pCat=$abc->getName();
    if($abc->getId()>1){
    $treeurl=$pCat.'/'.$treeurl;
    }
    $cate_cre1=$abc;
    }
    else{
    break;
    }
    }
    }
    $store = "default";
    $string .='"'.$store.'","'.$treeurl.'","'.$id.'","'.$cate_cre->getIsActive().'","'.$cate_cre->getMetaTitle().'","'.$cate_cre->getMetaKeywords().'","'.$cate_cre->getMetaDescription().'","'.$cate_cre->getIncludeInMenu().'","'.$cate_cre->getIsAnchor().'","'.$cate_cre->getDescription().'"';
    $string.="\n";
    }//endof if removeroot category and default category .
    }
    $csv_output = $heading ."\n".$string;
    $filename = "Categories";
    header("Content-type: application/vnd.ms-excel");
    header("Content-disposition: csv" . date("Y-m-d") . ".csv");
    header( "Content-disposition: filename=".$filename.".csv");
    print $csv_output;
    }
    ?>