<?php
/**
 * Created by PhpStorm.
 * User: nickc
 * Date: 3/1/2019
 * Time: 8:50 AM
 */

/*
 * To run this script, type php `batchFileRename.php --directory="path/to/dir"` into the command line
 * This will then rename all files in the directory according the Jon's specifications
 */


set_time_limit(6000);//set a long time limit because the directory listing will take a long time with the 75,00 files we have

//first, get the directory to rename the files in
foreach ($argv as $arg)
{
    if (strpos($arg, '--directory=') !== false) {
        $root= str_replace('--directory','',$arg);
        $root= str_replace('"','',$root);
        $root= str_replace('=','',$root);
        $root= str_replace(' ','',$root);
    }//if
}//foreach
if(empty($root))
{//throws an exception if no directory was selected.
    //default value
     $root = '/mnt/sfdc-site-photos';
    //$root = '/tmp/sfdc';
    // $directoryName = 'C:\test';
}

$directories = [
    "{$root}/BuildingExterior",
    "{$root}/Dispenser",
    "{$root}/MensUnisexRestroom",
    "{$root}/WomensRestroom",
    "{$root}/Overall",
    "{$root}/PID",

];



foreach ($directories as $directory)
{
    echo "\r\n ************************************ CHANGING DIRECTORY TO {$directory}************************************\r\n\r\n";
    renameDirectory($directory);
}

echo 'DONE!';



function renameDirectory($directoryName)
{
    echo "SCANNING DIRECTORY...THIS MAY TAKE A LONG TIME.\r\n";
    $files = SCANDIR($directoryName);
    echo "RENAMING JPG FILES IN DIRECTORY: {$directoryName}.\r\n";
    foreach ($files as $key=>$file)
    {
        set_time_limit(30);//set a long time limit because the directory listing will take a long time with the 75,00 files we have

        $pattern = '/([A-Za-z\d])\w+(_Previous.jpg)$/';//regex for a file that ends in our previous
        if(preg_match($pattern,$file))
        {
            echo "{$file} MATCHES!\r\n";
            $new  = str_replace('_Previous','_Deleted',$file);
            echo "renaming {$file} to {$new}\r\n";

             rename("{$directoryName}/{$file}","{$directoryName}/{$new}");//renames to _Deleted

        }
        else
        {
            echo "NOT A MATCH\r\n";

        }
    }


    foreach ($files as $file)
    {
        set_time_limit(30);//set a long time limit because the directory listing will take a long time with the 75,00 files we have

        $pattern = '/([A-Za-z\d])\w+(_Previous.jpg)$/';//regex for a file that ends in our previous
        $pattern2= '/([A-Za-z\d])\w+(_Deleted.jpg)$/';//regex for a file that ends in  previous or delete
        if(preg_match($pattern,$file)||preg_match($pattern2,$file))
        {
            //ignore

        }
        elseif(preg_match('/([A-Za-z\d])\w+(.jpg)$/',$file))
        {
            echo "{$file} IS A VALID JPG, SO LET'S RENAME IT\r\n";
            $new  = str_replace('.jpg','_Previous.jpg',$file);

            rename("{$directoryName}/{$file}","{$directoryName}/{$new}");//renames to _Previous

        }
    }
}



function oldRename($directoryName)
{
    echo "SCANNING DIRECTORY...THIS MAY TAKE A LONG TIME.\r\n";
    $files = SCANDIR($directoryName);
    echo "RENAMING JPG FILES IN DIRECTORY: {$directoryName}.\r\n";
    foreach ($files as $file)
    {
        set_time_limit(30);//set a long time limit because the directory listing will take a long time with the 75,00 files we have

        $pattern = '/([A-Za-z\d])\w+(_Previous.jpg)$/';//regex for a file that ends in our previous
        if(preg_match($pattern,$file))
        {
            echo "{$file} MATCHES!\r\n";
            //  $newFile = substr_replace($file,'',0,2);
            // $newFile = str_replace('.jpg','_Previous.jpg',$newFile);
            $search = str_replace('_Previous.jpg','.jpg',$file);

            //rename("{$directoryName}/{$file}","{$directoryName}/{$newFile}");
            //echo "RENAMED {$directoryName}/{$file} to {$directoryName}/{$newFile} \r\n";
            echo "Searching for {$search} \r\n";
            if(in_array($search,$files))
            {
                echo "Found a match, not renaming the old file\r\n";
            }
            else
            {
                echo "Didn't find a match, renaming file\r\n";
                rename("{$directoryName}/{$file}","{$directoryName}/{$search}");//renames to _Previous
                echo "RENAMED {$directoryName}/{$file} to {$directoryName}/{$search} \r\n";
            }
        }
        else
        {
            echo "NOT A MATCH\r\n";
            $pattern = '/([A-Za-z\d])\w+(.jpg)$/';//regex for a file that ends in our previous
            if(preg_match($pattern,$file))
            {
                echo "{$file} IS A VALID JPG, SO LET'S RENAME IT\r\n";
            }
        }

    }
}