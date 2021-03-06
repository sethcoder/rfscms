<?php
/////////////////////////////////////////////////////////////////////////////////////////
// RFSCMS http://www.rfscms.org/
/////////////////////////////////////////////////////////////////////////////////////////
// WIKI CORE MODULE
/////////////////////////////////////////////////////////////////////////////////////////
include_once("include/lib.all.php");

$RFS_ADDON_NAME="wiki";
$RFS_ADDON_VERSION="3.5.1";
$RFS_ADDON_SUB_VERSION="0";
$RFS_ADDON_RELEASE="";
$RFS_ADDON_DESCRIPTION="Wiki";
$RFS_ADDON_REQUIREMENTS="";
$RFS_ADDON_COST="";
$RFS_ADDON_LICENSE="";
$RFS_ADDON_DEPENDENCIES="";
$RFS_ADDON_AUTHOR="Seth T. Parson";
$RFS_ADDON_AUTHOR_EMAIL="seth.parson@rfscms.org";
$RFS_ADDON_AUTHOR_WEBSITE="http://rfscms.org/";
$RFS_ADDON_IMAGES="";
$RFS_ADDON_FILE_URL="";
$RFS_ADDON_GIT_REPOSITORY="";
$RFS_ADDON_URL=lib_modules_get_base_url_from_file(__FILE__);

lib_menus_register("Wiki","$RFS_SITE_URL/modules/core_wiki/wiki.php");
lib_access_add_method("wiki", "admin");
lib_access_add_method("wiki", "edit");
lib_access_add_method("wiki", "delete");
lib_access_add_method("wiki", "editothers");
lib_access_add_method("wiki", "deleteothers");
lib_access_add_method("wiki", "uploadfile");

/////////////////////////////////////////////////////////////////////////////////////////
// PANELS
function m_panel_wiki($x) {
	eval(lib_rfs_get_globals());
	echo "<h2>Last $x Wiki Pages</h2>";
	echo "<table width=100% border=0><tr>";
	echo "<td valign=top class=contenttd>";
	$res=lib_mysql_query("SELECT `name`, MAX( updated ) FROM `wiki` GROUP BY `name` ORDER BY MAX( updated ) DESC LIMIT 0,$x	");
	if($res) {
		while($page=$res->fetch_object()) {
			$opage=urlencode($page->name);
			echo "<a href=\"$RFS_SITE_URL/modules/core_wiki/wiki.php?name=$opage\" title=\"$page->name\">";
			echo lib_string_truncate($page->name,20);
			echo "</a> ";
			echo "<br>\n";
		}
	}
	echo "</td></tr></table>";
	echo "(<a href=\"$RFS_SITE_URL/modules/core_wiki/wiki.php?name=contents\" class=a_cat>More...</a>)";
}

function m_panel_wiki_one_page($x) {
    eval(lib_rfs_get_globals());
    $res=lib_mysql_query("select * from wiki where name='$x'");
	$wikipage=$res->fetch_object();
	$name=ucwords($name);
	lib_rfs_echo("<h1>$name</h1>");
    $res=lib_mysql_query("select * from wiki where name='$name'");
	$tpage=$res->fetch_object();
	$wikipage=lib_mysql_fetch_one_object("select * from wiki where name='$tpage->name' order by revision desc limit 1");
	if( ($action=="viewpagebyid") || ($id) ) { $wikipage=lib_mysql_fetch_one_object("select * from wiki where id='$id'"); }
    lib_rfs_echo(wikitext(wikiimg(($wikipage->text))));    
    
}
/////////////////////////////////////////////////////////////////////////////////////////
// FUNCTIONS
function wiki_img($text) {
// search for pattern {image.png,x,y} 
// replace with image box

	$r=preg_match('/(\{(.+)\.(.+))(,(.+))?(,(.+))?(\})+/', $text, $matches);
	foreach($matches as $k) {
		echo "$k <br>";
	}


}
function wikiimg($text) {
	eval(lib_rfs_get_globals());    
    $text=stripslashes($text);
    $text=str_replace("{{","&#123;",$text);
    $text=str_replace("}}","&#125;",$text);
	
    $outtext="";
    $ila=explode("{",$text);
    for($i=0;$i<count($ila);$i++)    {
        if(stristr($ila[$i],"}"))        {
            $ila2=explode("}",$ila[$i]);

				$imgxvars=explode(",",$ila2[0]);
				$img="$RFS_SITE_WIKI_IMAGES_PATH/". urlencode($imgxvars[0]);

				$w=$imgxvars[1];
				$h=$imgxvars[2];

				if(!file_exists($img)) {
					$outtext.= "<table border=0 class=warning><tr><td class=warning> ";
					$outtext.= lib_images_thumb("$RFS_SITE_WIKI_IMAGES_PATH/wiki_warning.png",32,32,1);
					$outtext.="</td><td class=warning> Image not found";
					//////////////////////////////////////////////////////////////////////////////
					if(lib_access_check("wiki","uploadfile")) {
                    $outtext.=" </td><td class=warning> <form enctype=\"multipart/form-data\" action=\"$addon_folder\" method=\"post\">\n";
                    $outtext.="<input type=hidden name=give_file value=yes>\n";
                    $outtext.="<input type=hidden name=name value=\"$name\">\n";
                    $outtext.="<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"99900000\">";
                    $outtext.="<input type=hidden name=short_name value=\"$img\">";
                    $outtext.="<input name=\"userfile\" type=\"file\">";
                    $outtext.="<input type=\"submit\" name=\"submit\" value=\"upload\">\n";
                    $outtext.="</form>\n";
                }
					$outtext.="</td></tr></table>";
                //////////////////////////////////////////////////////////////////////////////
            } else {
				if( ($w) || ($h)) {                    
					$outtext.= lib_images_thumb("$RFS_SITE_PATH/$img",$w,$h,1);
                }
				else {
                    $outtext.="<img src=\"$RFS_SITE_URL/".$img."\" border=0 >";                    
                }

				if(lib_access_check("wiki","uploadfile")) {
				// $outtext.="[<a href=\"$RFS_SITE_URL/modules/wiki/wiki.php?action=edit_image\">Edit Image</a>]";
			}

		}
            $outtext.=$ila2[1];
        }
        else
            $outtext.=$ila[$i];
    }
    return $outtext;
}
function wikicode($text) {
	$ila=explode("[",$text);
    for($i=0;$i<count($ila);$i++) {
        if(stristr($ila[$i],"]")) {
            $ila2=explode("]",$ila[$i]);
			$fnc= $ila2[0][0];			
            switch($fnc) {
				
				case "#":
                       
                    $fnc_=explode(",",substr($ila2[0],1));
                    $fnc=$fnc_[0];
                    $ar1=$fnc_[1];
                    $ar2=$fnc_[2];
					
				switch($fnc) {
					
					case "shellstart":
					case "ss":
						$ila2[1]=str_replace("$","&#36;",$ila2[1]);
					case "codestart":
						$ila2[1]=str_replace("{","&#123;",$ila2[1]);
						$ila2[1]=str_replace("}","&#125;",$ila2[1]);
						
						$outtext.="[".$ila2[0]."]".$ila2[1];
					break;
			
				default:
					$outtext.="[".$ila2[0]."]".$ila2[1];
					break;
				}

			}
        }
		else
            $outtext.=$ila[$i];
	}    
	return $outtext;
}
function wikitext($text) {
	eval(lib_rfs_get_globals());
	
	// if(empty($RFS_SITE_WIKI_BULLET_IMAGE)) $RFSW_BULLET_IMAGE	= $RFS_SITE_URL."/modules/core_wiki/images/bullet.gif";	if(empty($RFS_SITE_WIKI_LINK_IMAGE))   $RFSW_LINK_IMAGE   = $RFS_SITE_URL."/modules/core_wiki/images/link2.png";
	// $text=wikicode($text);
	// $text= wikiimg($text);

	$text=str_replace("[[","&#91;",$text);
	$text=str_replace("]]","&#93;",$text);
	$text=str_replace("$$","&#36;",$text);	
	$text=str_replace("</h1>\r\n","</h1>",$text);
	$text=str_replace("</h2>\r\n","</h2>",$text);
	$text=str_replace("</h3>\r\n","</h3>",$text);
	$text=str_replace("\r\n<hr>","<hr>",$text);
	$text=str_replace("<hr>\r\n","<hr>",$text);
	$text=str_replace("<hr>\n","<hr>",$text);
	$text=str_replace("<?","&lt;?",$text);
	$text=str_replace("?>","?&gt;",$text);	
	$text=str_replace("^^","&#94;",$text);	    
   
	
	$text=lib_string_get_twitter_code($text);
	$text=lib_string_get_email_code($text);
	$text=lib_string_get_url_code($text);
	
    $outtext="";
    $ila=explode("[",$text);
    for($i=0;$i<count($ila);$i++) {
        if(stristr($ila[$i],"]")) {
            $ila2=explode("]",$ila[$i]);
            $fnc= $ila2[0][0];
			
            switch($fnc) {
								
                case "@":                 
                    // symbolic page link
                    $fnc=explode(",",substr($ila2[0],1));
						$outtext.="<a class=rfswiki_link href=\"$addon_folder?name=".urlencode($fnc[0])."\">".$fnc[1]."</a>";
						$outtext.=nl2br($ila2[1]);						
                    break;
                    
                case "#":
                        
                    // list
                   
                    $fnc_=explode(",",substr($ila2[0],1));
                    $fnc=$fnc_[0];
					$ar1=$ar2="";
                    if(isset($fnc_[1])) $ar1=$fnc_[1];
                    if(isset($fnc_[2])) $ar2=$fnc_[2];
                                  
                    d_echo($fnc." ".$ar1." ".$ar2);
                    
                    $fnc=strtolower($fnc);
                    
                    if($GLOBALS['RFS_DEBUG']=="yes")
                        $outtext.=" # FUNCTION $fnc()\n{";                        
						
						if( ($fnc=="toggledivstart") ||
						    ($fnc=="tds") ) {
							
							
							$lstd=explode(",",$ila2[0]);
							$outtext.=rfs_togglediv_start_ne($lstd[1],$lstd[2],$lstd[3]);							
							$outtext.=$ila2[1];
							
							
						}
						if( ($fnc=="toggledivend") ||
							($fnc=="tde") ) {
							
							$outtext.=rfs_togglediv_end_ne();
							$outtext.="<br>";
							$outtext.=$ila2[1];
							
							
						}
                        
						
                    if(	($fnc=="shellstart") ||
							($fnc=="ss") ) {
								
								

                        $outtext.="<div class='wikishell'><BR>";
							$xx=$ila2[1];
							$xx=str_replace("<br />","<br /> " ,$xx);

                        $outtext.="<pre>".$xx;
                        $outtext.="<br><br></div>";
                    }
                    if(	($fnc=="shellend") ||
							($fnc=="se")) {
								
                        $outtext.="</pre>".nl2br($ila2[1]);                        
                    }
                        
                    if( ($fnc=="codestart") || 
					    ($fnc=="cs") ) {
							
							$language=$ar1;
							if(empty($language)) $language="php";
						
							include_once("$RFS_SITE_PATH/3rdparty/geshi/geshi.php");
							$ila2[1]=html_entity_decode($ila2[1]);
							$geshi=new GeSHi($ila2[1],$language);
							
							$code=$geshi->parse_code();
							$code=str_replace("class=","class='codez' ff=",$code);
							
							$outtext.=$code;
							
							
                    }
                    if( ($fnc=="codeend") || 
						($fnc=="ce") ) {
					
                        $outtext.=nl2br($ila2[1]);
                    }
                        
                    if( ($fnc=="beginlist") ||
					    ($fnc=="bl") ) {
						
                        $outtext.="<table class=rfs_bulletlist width=100%>";
                        $outtext.="<tr><td class=rfs_bulletlist_txt_td>";
                        $outtext.="<table border=0>";
                        $lstd=explode("\n",$ila2[1]);
                        for($li=0;$li<count($lstd);$li++)
                        {
                            $lstd[$li]=str_replace("\r","",$lstd[$li]);
                            $lstd[$li]=str_replace("\n","",$lstd[$li]);
                            if(!empty($lstd[$li]))
                            {
                                $outtext.= "<tr><td class=rfs_bulletlist_txt_td width=20></td>";
                                $outtext.= "<td class=rfs_bulletlist_img_td>";								
                                $outtext.= " <img src=\"$RFSW_BULLET_IMAGE\">";
                                $outtext.= "</td><td class=rfs_bulletlist_txt_td>";
                                $outtext.= $lstd[$li];
                                $outtext.= "</td></tr>";
                            }
                        }

                        $outtext.="</table>";
                        $outtext.= "</td></tr>";
                        $outtext.="</table>";
                    }
                    if( ($fnc=="endlist") || 
					    ($fnc=="el") ) {
                        $outtext.=nl2br($ila2[1]);
                    }
					
                    if($GLOBALS['RFS_DEBUG']=="yes")
                        $outtext.=" }\n";
                    break;

                default:
					
						if( 	stristr($ila2[0],"http:") || 
								stristr($ila2[0],"https:") ||
								stristr($ila2[0],"ftp:") ||
								stristr($ila2[0],"ftps:") ) {
								
									$outlink   = $ila2[0];
									$shortname = $ila2[0];
									$target    = "_blank";
									
									$exchk=explode(",",$outlink);
									if(count($exchk)>1) {
										$shortname=$exchk[0];
										$outlink=$exchk[1];
										if(!empty($exchk[2])) 
											$target=$exchk[2];
									}
									
									$outlink=str_replace(":","_rfs_colon_",$outlink);
									$outlink=urlencode($outlink);
									
									
									$outtext.="<a class=rfswiki_link href=$RFS_SITE_URL/link_out.php?link=$outlink target=\"$target\">".$shortname;
									$outtext.="  <img src=\"$RFS_SITE_URL/modules/core_wiki/images/link2.png\"
									 border=\"0\" width=\"11\" height=\"10\" ></a> ";
									$outtext.=nl2br($ila2[1]);
							}
						else
							$outtext.="<a class=rfswiki_link href=\"$addon_folder?name=".urlencode($ila2[0])."\">".$ila2[0]."</a>".nl2br($ila2[1]);
							
					
					
                    break;
			}
			
        }
        else
            $outtext.=nl2br($ila[$i]);
    }
	
    return $outtext;
}



?>
