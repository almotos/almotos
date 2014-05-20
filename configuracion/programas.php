<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Base
 * @author      Francisco J. Lozano n. <fjlozano@felinux.com.co>
 * @author      Julian A. Mondragón <jmondragon@felinux.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2009 FELINUX LTDA
 * @version     0.1
 *
 **/

/**
 *
 * Rutas de los programas (binarios ejecutables del sistema operativo) utilizados
 *
 */
$configuracion["PROGRAMAS"] = array(
    "convert"   => "/usr/bin/convert -resize %1 %2 %3",
    "ffmpeg"    => "/usr/bin/ffmpeg -y -i %1 -f flv -vcodec flv -threads 4 -s 320x240 -r 30.00 -pix_fmt yuv420p -g 300 -qmin 3 -b 512k -async 50 -acodec libmp3lame -ar 11025 -ac 2 -ab 16k %2",
);

?>