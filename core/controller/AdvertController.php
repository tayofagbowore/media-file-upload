<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdvertController
 *
 * @author user
 */
class AdvertController extends BaseController {

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Advert
     */
    protected $advert;

    /**
     * @var Plate
     */
    protected $plate;

    /**
     * @var MonitorAdvert
     */
    protected $monitorAdvert;

    protected function initModel() {
        $this->client = new Client();
        $this->advert = new Advert();
        $this->plate = new Plate();
        $this->monitorAdvert = new MonitorAdvert();
    }

    private function addAdvert($array) {
        if (!empty($array)) {
            $array = array_filter($array);

            if ($this->advert->isAdvertExisting( $array[AdvertTable::advert_name])) {
                return false;
            } else {
                $advert_id = $this->advert->addAdvert($array);
                if ($advert_id == 0) {
                    return false;
                } else {
                    return $advert_id;
                }
            }
        }
        return false;
    }

    private function addPlate($array) {
        $array = array_filter($array);
        $plate_id = $this->plate->addPlate($array);
        if ($plate_id == 0) {
            return MessageHandler::message(MessageHandler::STATUS_ERROR, 'Could not save plate');
        } else {
            $output = array();
            $output[PlateTable::table_name] = $this->plate->fetchData(array(PlateTable::plate_id => $plate_id));
//                    var_dump($output);
            return MessageHandler::success($output, false);
        }
    }

    public function fileUpload($username, $file_input_name, $advert_type_id, $advert_name, $start_date, $advert_span) {

        //checks if user is existing
        $isexisting = $this->client->isUsernameExisting($username);
        $video = array("video/avi", "video/msvideo", "video/x-msvideo", "video/mpeg", "video/mpeg", "video/x-flv", "video/mp4", "video/x-ms-wmv"); //allowed video mime types
        $image = array("image/jpeg", "image/pjpeg", "image/png");//allowed image mime types

        //checks if the file name hase a dot so as not to confuse extension
        if (substr_count(preg_replace('/\s+/', '', $_FILES[$file_input_name]["name"][0]), '.') > 1){
            echo    "<script type='text/javascript'>
                           window.top.doneLoading('<div>File name contains dot(.)</div>');
                        </script>";
            return;
        }

        $ar = array();
        $arr = array();
        $update_arr = array();
        if ($isexisting != false) {
            $client_details = $this->client->getCompanyDetails($username);
            $client_id = $client_details[0][ClientTable::client_id];//obtaining client id
            if (!in_array($_FILES[$file_input_name]["type"][0], $video) && !in_array($_FILES[$file_input_name]["type"][0], $image)){

                echo    "<script type='text/javascript'>
                           window.top.doneLoading('<div>File Extension not Allowed</div>');
                        </script>";
                return;
            }

            else if (in_array($_FILES[$file_input_name]["type"][0], $video)) { //if the file is a video

                $name_of_file = $client_id.preg_replace('/\s+/', '', $_FILES[$file_input_name]["name"][0]);
                $fileuploader = new FileUpload($file_input_name, array("jpg", "png", "jpeg", "jif", "gif", "avi", "flv", "mp4", "mov", "wmv", "mpeg", "mpg", "mpe"), array("image/jpeg", "image/pjpeg", "image/pjpeg", "application/x-troff-msvideo", "image/gif", "image/png", "video/avi", "video/msvideo", "video/x-msvideo", "video/mpeg", "video/mpeg", "video/x-flv", "video/flv", "video/mp4", "video/quicktime", "video/x-ms-wmv", "application/octet-stream"), 10485760);
                $fileStatus = $fileuploader->saveFile($name_of_file, "AdvertFileBank", 0); //do an if statement to check the file size

                if ($fileStatus == 5){
                    echo    "<script type='text/javascript'>
                               window.top.doneLoading('<div>File exists; Please Rename File</div>');
                            </script>";
                    return;
                }else if ($fileStatus == 3){
                    echo    "<script type='text/javascript'>
                               window.top.doneLoading('<div>File Extension not Allowed</div>');
                            </script>";
                    return;
                }else if ($fileStatus == 6){
                    echo    "<script type='text/javascript'>
                               window.top.doneLoading('<div>File Too Large</div>');
                            </script>";
                    return;
                }else if ($fileStatus == 4){
                    echo    "<script type='text/javascript'>
                               window.top.doneLoading('<div>File Extension not Allowed</div>');
                            </script>";
                    return;
                }else if ($fileStatus == 1){

                    $advert_url = ROOT_PATH . 'AdvertFileBank/' . $name_of_file;
                    //echo $advert_url;
                    /*
                     * commands
                     * -i Input file name
                     * -an Disable audio
                     * -ss Get image from X seconds in the video
                     * -s Size of the image
                     */

                    $ffmpeg = FFMPEG_PATH;
                    $imageFile = "Thumbnail." . $name_of_file . ".jpg";
                    $videoFile = ADVERT_FILE_BANK."/".$name_of_file;
                    $size = "100x75";
                    $getFromSecond = 5;

                    $cmd = "$ffmpeg -an -ss $getFromSecond -i $videoFile -vframes 1 -s $size '".THUMBNAIL_FILE_BANK."'/$imageFile";
                    exec($cmd . ' 2>&1', $outputed, $retrival);

                    if ($retrival == 0) {
                        echo    "<script type='text/javascript'>
                               window.top.doneLoading('<div>thumbnail created</div>');
                            </script>";
                    } else {
                        echo "<script type='text/javascript'>
                               window.top.doneLoading('<div>error in creating thumbnail</div>');
                            </script>";
                        return;
                    }

                    $thumb_nail = ROOT_PATH . 'ThumbnailFileBank/' . $imageFile;

                    $ar[AdvertTable::client_id] = $client_id;
                    $ar[AdvertTable::advert_type_id] = $advert_type_id;
                    $ar[AdvertTable::advert_span] = $advert_span;
                    $ar[AdvertTable::advert_url] = $advert_url;
                    $ar[AdvertTable::advert_name] = $advert_name;
                    $ar[AdvertTable::thumb_nail] = $thumb_nail;
                    $ar[AdvertTable::start_date] = $start_date;

                    //do a check if the advert is existing
                    $advertExisting = $this->advert->isAdvertExisting($advert_name);
                    if ($advertExisting == true){
                        echo "<script type='text/javascript'>
                               window.top.doneLoading('<div>Advert already existing</div>');
                            </script>";
                        return;
                    }
                    else{
                        $result = $this->addAdvert($ar);
                        if($result == false){
                            echo "<script type='text/javascript'>
                               window.top.doneLoading('<div>Could not add advert</div>');
                            </script>";
                            return;
                        }
                        elseif($result > 0){
                            echo "<script type='text/javascript'>
                               window.top.doneLoading('<div>Advert Upload Successful! Thank You</div>');
                            </script>";
                            return;
                        }
                    }
                }

            } else if (in_array($_FILES[$file_input_name]["type"][0], $image)) {

                $ar[AdvertTable::client_id] = $client_id;
                $ar[AdvertTable::advert_type_id] = $advert_type_id;
                $ar[AdvertTable::advert_span] = $advert_span;
                $ar[AdvertTable::advert_url] = 'NULL';
                $ar[AdvertTable::advert_name] = $advert_name;
                $ar[AdvertTable::thumb_nail] = 'NULL';
                $ar[AdvertTable::start_date] = $start_date;

                //check if advert is existing
                $advertExisting = $this->advert->isAdvertExisting($advert_name);
                if ($advertExisting == true){
                    echo "<script type='text/javascript'>
                               window.top.doneLoading('<div>Advert already existing</div>');
                            </script>";
                    return;
                }
                else {
                    $output = $this->addAdvert($ar);
                    if($output == false){
                        echo "<script type='text/javascript'>
                               window.top.doneLoading('<div>Could not add advert</div>');
                            </script>";
                        return;
                    }
                    elseif($output > 0){
                        $advert_id = $output;

                        for ($i = 0; $i < count($_FILES[$file_input_name]['name']); ++$i) {
                            if (in_array($_FILES[$file_input_name]['type'][$i], $image)) {
                                $fileuploader = new FileUpload($file_input_name, array("jpg", "png", "jpeg", "jif", "gif", "avi", "flv", "mp4", "mov", "wmv", "mpeg", "mpg", "mpe"), array("image/jpeg", "image/pjpeg", "image/pjpeg", "application/x-troff-msvideo", "image/gif", "image/png", "video/avi", "video/msvideo", "video/x-msvideo", "video/mpeg", "video/mpeg", "video/x-flv", "video/flv", "video/mp4", "video/quicktime", "video/x-ms-wmv", "application/octet-stream"), 10485700);
                                $name_of_file ="plate" . $advert_id . ".00". ($i + 1) . ".png";
                                $plate_url = ROOT_PATH . 'PlateFileBank/' . $name_of_file;

                                $fileStatus1 = $fileuploader->saveFile($name_of_file, "PlateFileBank", $i);

                                if ($fileStatus1 == 5){
                                    echo    "<script type='text/javascript'>
                               window.top.doneLoading('<div>File exists; Please Rename File</div>');
                            </script>";
                                    return;
                                }else if ($fileStatus1 == 3){
                                    echo    "<script type='text/javascript'>
                               window.top.doneLoading('<div>File Extension not Allowed</div>');
                            </script>";
                                    return;
                                }else if ($fileStatus1 == 6){
                                    echo    "<script type='text/javascript'>
                               window.top.doneLoading('<div>File Too Large</div>');
                            </script>";
                                    return;
                                }else if ($fileStatus1 == 4){
                                    echo    "<script type='text/javascript'>
                               window.top.doneLoading('<div>File Extension not Allowed</div>');
                            </script>";
                                    return;
                                }else if ($fileStatus1 == 1){
                                    $arr[PlateTable::advert_id] = $advert_id;
                                    $arr[PlateTable::plate_url] = $plate_url;
                                    $this->addPlate($arr);
                                }
                            }

                        }

                        //images should be in png
                        $pictures_file_path = 'cd '.PLATE_FILE_BANK;

                        $video_cmd = $pictures_file_path.' && ffmpeg -r 1/10 -i "plate'.$advert_id.'.%03d.png" -vcodec libx264 -vf "fps=25,format=yuv420p" -s 500x500 "output'.$advert_id.'.mp4"';
//                $video_cmd = 'cd C:\wamp\www\kakaki\PlateFileBank && c:\\ffmpeg\\bin\\ffmpeg -r 0.5 -i "plate130.%03d.jpg" -vcodec libx264 -vf "fps=25,format=yuv420p" "output130.mp4"';

                        exec($video_cmd . ' 2>&1', $outputed2, $retrival2);
//                echo '<script type="text/javascript">alert("Data has been submitted to ' . $retrival2 . '");</script>';
                        if ($retrival2 == 0) {

                            //this sections
                            //done with creating the video, next COPY one of the pictures used in creating the video to the
                            //thumbnail folder to serve as the video thumbnail and MOVE the video itself to video folder

                            $image_origin = IMAGE_ORIGIN . $advert_id . ".002.png";
                            $image_destination = THUMBNAIL_FILE_BANK;
                            $video_origin = VIDEO_ORIGIN.$advert_id.'.mp4';
                            $video_destination = ADVERT_FILE_BANK;
                            $move_cmd = 'cp '.$image_origin.' '.$image_destination.' && mv '.$video_origin.' '.$video_destination;

                            exec($move_cmd . ' 2>&1', $outputed3, $retrival3);

                            //update the advert table with thumbnail_url and video_url

                            $advert_thumbnail = ROOT_PATH . 'ThumbnailFileBank/plate'. $advert_id . '.002.png';
                            $new_advert_url = ROOT_PATH. 'AdvertFileBank/output'. $advert_id .'.mp4';

                            $this->updateAdvert($advert_id, $new_advert_url, $advert_thumbnail);

                            echo "<script type='text/javascript'>
                        window.top.doneLoading('<div>video created</div>');
                    </script>";
                        } else {

                            $this->advert->inActive($advert_id);
                            echo "<script type='text/javascript'>
                        window.top.doneLoading('<div>Error creating video, Advert not Created</div>');
                    </script>";
                            return;

                        }

                        //MOVE /Y filepath destination

                        //couple the images together to form video and also get thumbnail
                        //ffmpeg -r 0.5 -i "input files eg img%03d.jpg" -vcodec libx264 "output file"
                        //remember that the ffmpeg command doesnt run with file path, so u need to first give a command to navigate to the directory that contains the pictures before issueing the ffmpeg command
                        //after creating the video, and getting the thumbnail, you update the advert table

                        echo "<script type='text/javascript'>
                               window.top.doneLoading('<div>Advert Upload Successful</div>');
                            </script>";
                    }
                }



            }
        } else {
            echo "<script>
                   window.top.doneLoading('<div>Error Creating Advert; File Size Too Large</div>');
                </script>";
            return;
        }
        //add advert passing in an array containing $file_path, $client_id, $advert_type, $duration
    }

}
?>


