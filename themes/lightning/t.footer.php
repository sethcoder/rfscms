<?php
	
	if(!lib_rfs_bool_true($data->donated)) {
		echo "<center>";
		lib_social_google_adsense($RFS_SITE_GOOGLE_ADSENSE);
		echo "</center>";
	}
	else {
		echo "<p>&nbsp;</p>";
		
	}
	echo "</td>";
	
	echo "<td class=\"righttd\" valign=top>";

		lib_modules_draw("right");
		
		
	echo "</td></tr>";
	echo "</table>";

	
	echo "<br>";
	echo "<br>";
	echo "<br>";
    echo "<div style='float: right;'>$RFS_SITE_COPYRIGHT</div>";
	
	echo "<br>";
	echo "<br>";
	
	
    echo "</body></html>";

?>
