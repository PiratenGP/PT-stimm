<?php
wp_enqueue_style( "pt-stimm", plugin_dir_url(__FILE__)."style.css" );
$style = get_option("pt_stimm_style");
wp_add_inline_style( "pt-stimm", $style );
class PT_stimm_punkt {
	public $id;
	public $text;
	public $rislink;
	public $stimmen = array();
	
	public function __construct($id, $text, $rislink) {
		$this->id = $id;
		$this->text = $text;
		$this->rislink = $rislink;
	}
	
	public function editStimme($mitglied, $stimme) {
		$this->stimmen[$mitglied->getID()] = $stimme;
	}
	
	public function getID() {
		return $this->id;
	}
	
}

class PT_stimm_sitzung {
	public $id;
	public $name;
	public $datum;
	public $linkzurto;
	public $punkte = array();
	public $nextID = 0;
	
	public function __construct($id, $name, $datum, $linkzurto = "") {
		$this->id = $id;
		$this->name = $name;
		$this->datum = $datum;
		$this->linkzurto = $linkzurto;
	}
	
	public function addPunkt($text, $rislink) {
		$newID = $this->nextID++;
		$this->punkte[$newID] = new PT_stimm_punkt($newID, $text, $rislink);
	}
	
	public function getID() {
		return $this->id;
	}
	
}

class PT_stimm_mitglied {

	public $id;
	public $name;
	public $kuerzel;
	public $partei;
	
	public function __construct($id, $name, $kuerzel, $partei) {
		$this->id = $id;
		$this->name = $name;
		$this->kuerzel = $kuerzel;
		$this->partei = $partei;
	}
		
		
	public function getID() {
		return $this->id;
	}
}

class PT_stimm_gremium {

	public $id;
	public $name;
	public $mitglieder = array();
	public $sitzungen = array();
	public $sitzungenByDatum = array();
	public $nextID = array("mitglieder" => 0, "sitzungen" => 0);

	public function __construct($id, $name) {
		$this->id = $id;
		$this->name = $name;
	}
	
	public function addMitglied($name, $kuerzel, $partei) {
		$newID = $this->nextID['mitglieder']++;
		$this->mitglieder[$newID] = new PT_stimm_mitglied($newID, $name, $kuerzel, $partei);
	}
	
	public function addSitzung($text, $datum, $linkzurto) {
		$newID = $this->nextID['sitzungen']++;
		$this->sitzungen[$newID] = new PT_stimm_sitzung($newID, $text, $datum, $linkzurto);
		$this->sitzungenByDatum[$datum][$newID] = &$this->sitzungen[$newID];
	}
	
	public function getID() {
		return $this->id;
	}
	
}

class PT_stimm {

	static $stimmen = array(
		array(
			"name"		=>	"Keine Angabe",
			"kuerzel"	=>	"?",
			"class"		=>	"stimm-ka",
		),
		array(
			"name"		=>	"Zustimmung",
			"kuerzel"	=>	"",
			"class"		=>	"stimm-yes",
		),
		array(
			"name"		=>	"Ablehnung",
			"kuerzel"	=>	"",
			"class"		=>	"stimm-no",
		),
		array(
			"name"		=>	"Enthaltung",
			"kuerzel"	=>	"",
			"class"		=>	"stimm-x",
		),
		array(
			"name"		=>	"Geheim",
			"kuerzel"	=>	"G",
			"class"		=>	"stimm-secret",
		),
		array(
			"name"		=>	"Nicht abgestimmt",
			"kuerzel"	=>	"",
			"class"		=>	"stimm-na",
		),
	
	
	);

	
	static function get_data($url)
	{
	  $ch = curl_init();
	  $timeout = 5;
	  curl_setopt($ch,CURLOPT_URL,$url);
	  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	  $data = curl_exec($ch);
	  curl_close($ch);
	  return $data;
	}
	
	static function getRISTO($url) {
		$content = PT_stimm::get_data($url);
		$content = substr($content, strpos($content, "Tagesordnung</b>")+16);


		preg_match_all('/<table.*?>(.*?)<\/table>/si', $content, $matches);
		preg_match_all('/<tr.*?>(.*?)<\/tr>/si', $matches[1][0], $matches2);

		$rows = $matches2[1];
		foreach ($rows as $key => $val) {
			preg_match_all('/<td.*?>(.*?)<\/td>/si', $val, $matches3);
			//$cols[$key] = $matches3[1];
			$cols[$key]['name'] = utf8_encode(strip_tags($matches3[1][4]));
			$rawlink = $matches3[1][3];
			preg_match_all('/<input .*? value="(.*?)">/si', $rawlink, $matches4);
			$cols[$key]['link'] = "https://goeppingen.more-rubin1.de/beschluesse_details.php?nid=".$matches4[1][1]."&vid=".$matches4[1][0]."&status=1";
		}
		return $cols;
	}
	
	static public function shortcode_legende($atts) {
			ob_start();
			include('shortcode/legende.php');
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
	}
	
	static public function shortcode($atts) {
		$id = $atts['id'];
		$ext = $atts['ext'];
		$mitglieder = $atts['mitglieder'];
		if ($mitglieder != "") {
			$mitglieder = explode(",", $mitglieder);
		}
		$options = get_option("pt_stimm");
		$style = get_option("pt_stimm_style");
		
		if (!$id && $ext) {
			if ($options['gremien-ext'][$ext]) {
				$url = $options['gremien-ext'][$ext]['url'];
				if ((time()-$options['gremien-ext'][$ext]['datadate']) > 900) {
					$content = PT_stimm::get_data($url);
					if ($content != "") {
						$options['gremien-ext'][$ext]['data'] = JSON_decode($content);
						$options['gremien-ext'][$ext]['datadate'] = time();
						update_option("pt_stimm", $options);
					}
				}
				$gremium = $options['gremien-ext'][$ext]['data'];
			} else {
				$gremium = false;
			}
		} else {
			if ($options['gremien'][$id]) {
				$gremium = $options['gremien'][$id];
			} else {
				$gremium = false;
			}
		}
		
		$hidefrom = $atts['hidefrom'];
		if (!$hidefrom || !is_numeric($hidefrom) || ($hidefrom < 1)) {
			$hidefrom = 0;
		}
		
		if ($gremium != false) {
			ob_start();
			include('shortcode/list.php');
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		} else {
			return "";
		}
	
	}
	


	static public function adminmenu() {
		
		
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
		if ($_GET['reset'] == "1") update_option("PT_stimm", null);
		
		$options = get_option("pt_stimm");
		$style = get_option("pt_stimm_style");
		
		$page = $_REQUEST['pt-stimm-page'];
		if (!$page) $page = "home";
		
		
		
		if (!is_array($options)) {
			$options = array(
				"nextID" => 1,
				"gremien" => array(),
				"nextID_ext" => 1,
				"gremien_ext" => array(),
			);
		}
		
		if ($style == "") {
			$style = <<<STYLE
/* Farben */

.pt-stimm-stimme.stimm-ka { /* Keine Angabe */
	/* background-color: ; */
	/* color: ; */
}
.pt-stimm-stimme.stimm-yes { /* Zugestimmt */
	background-color: #33B821;
	/* color: ; */
}
.pt-stimm-stimme.stimm-no { /* Abgelehnt */
	background-color: #CF6363;
	/* color: ; */
}
.pt-stimm-stimme.stimm-x { /* Enthaltung */
	background-color: #EDE15A;
	/* color: ; */
}
.pt-stimm-stimme.stimm-secret { /* Geheime Abstimmung */
	background-color: #000000;
	color: #ffffff;
}
.pt-stimm-stimme.stimm-na { /* Nicht abgestimmt */
	/* background-color: ; */
	/* color: ; */
}
STYLE;
		update_option("PT_stimm_style", $style);
		}
		
		$error = array();
		$success = array();
		if ($_POST['pt-stimm-action'] == "gremium-add") {
			$newID = $options['nextID']++;
			
			$data_name = htmlspecialchars(trim(stripslashes($_POST['pt-stimm-name'])));
			
			if ($data_name == "") $error[] = "Name";
			
			if (count($error) == 0) {
				$options['gremien'][$newID] = new PT_stimm_gremium($newID, $data_name);
				update_option("pt_stimm", $options);
				
				$success[] = "Gremium hinzugefügt.";
			}
			$page = "home";
		}
		
		if ($_POST['pt-stimm-action'] == "gremium-import-file") {
			if ($_FILES['pt-stimm-import-file']['error'] == UPLOAD_ERR_OK  && is_uploaded_file($_FILES['pt-stimm-import-file']['tmp_name'])) {
				$content = file_get_contents($_FILES['pt-stimm-import-file']['tmp_name']); 
				$content = JSON_decode($content, true);
				if (!is_array($content)) {
					$error[] = "Datei nicht gültig.";
				} else {
					
					while (true) {
						//Gremium
						$data_name = trim($content['name']);
						if ($data_name == "") { $error[] = "Fehler."; break; }
						$newID = $options['nextID']++;
						$options['gremien'][$newID] = new PT_stimm_gremium($newID, $data_name);
						$gremium = &$options['gremien'][$newID];
						
						//Mitglieder
						$mitglieder = $content['mitglieder'];
						if (is_array($mitglieder) && (count($mitglieder)>0)) {
							$hn = 0;
							foreach ($mitglieder as $val) {
								$id = $val['id'];
								$data_name = $val['name'];
								$data_kuerzel = $val['kuerzel'];
								$data_partei = $val['partei'];
								
								$gremium->mitglieder[$id] = new PT_stimm_mitglied($id, $data_name, $data_kuerzel, $data_partei);
								
								if ($id > $hn) $hn = $id;
								
							}
							$gremium->nextID['mitglieder'] = $hn+1;
						}
						
						//Sitzungen
						$sitzungen = $content['sitzungen'];
						if (is_array($sitzungen) && (count($sitzungen)>0)) {
							$hn = 0;
							foreach ($sitzungen as $val) {
								$id = $val['id'];
								$data_name = $val['name'];
								$data_datum = $val['datum'];
								$data_linkzurto = $val['linkzurto'];
								
								$gremium->sitzungen[$id] = new PT_stimm_sitzung($id, $data_name, $data_datum, $data_linkzurto);
																
								$gremium->sitzungenByDatum[$data_datum][$id] = &$gremium->sitzungen[$id];
								
								$punkte = $val['punkte'];
								if (is_array($punkte) && (count($punkte)>0)) {
									foreach ($punkte as $valp) {
										$idp = $valp['id'];
										$data_text = $valp['text'];
										$data_rislink = $valp['rislink'];
										
										$gremium->sitzungen[$id]->punkte[$idp] = new PT_stimm_punkt($idp, $data_text, $data_rislink);
										
										$stimmen = $valp['stimmen'];
										if (is_array($stimmen) && (count($stimmen)>0)) {
											foreach ($stimmen as $keys => $vals) {
												$gremium->sitzungen[$id]->punkte[$idp]->editStimme($gremium->mitglieder[$keys], $vals);
											}
										}
									}
								}
								
								if ($id > $hn) $hn = $id;
							}
							$gremium->nextID['sitzungen'] = $hn+1;
						}
					
						break;
					}
					update_option("PT_stimm", $options);
					$success[] = "Erfolgreich importiert.";
					}
			} else {
				$error[] = "Fehler.";
			}
		}
		
		if ($_POST['pt-stimm-action'] == "gremium-del") {
			$delID = $_POST['pt-stimm-gremium-id'];
			if ($options['gremien'][$delID]) {
				unset($options['gremien'][$delID]);
				update_option("pt_stimm", $options);
				$success = "Gremium wurde gelöscht.";
			} else {
				$error[] = "Gremium konnte nicht gelöscht werden.";
			}
			
			$page = "home";
		}
		
		if ($_POST['pt-stimm-action'] == "gremium-edit") {
			$gremiumid = $_POST['pt-stimm-gremium-id'];
			$data_name = htmlspecialchars(trim(stripslashes($_POST['pt-stimm-gremium-name'])));
			
			if ($data_name == "") $error[] = "Name";
			if (!$options['gremien'][$gremiumid]) $error[] = "Fehler.";
			
			if (count($error) == 0) {
				$options['gremien'][$gremiumid]->name = $data_name;
				update_option("pt_stimm", $options);
				
				$success[] = "Gremium geändert.";
			}
			$page = "gremium-edit";
		}
		
		if ($_POST['pt-stimm-action'] == "style-edit") {
			$style = htmlspecialchars(trim(stripslashes($_POST['pt-stimm-style'])));
			
			
			update_option("pt_stimm_style", $style);
			
			$success[] = "Stylesheet geändert.";
			
			$page = "home";
		}
		
		if ($_POST['pt-stimm-action'] == "gremium-ext-add") {
			$newID = $options['nextID_ext']++;
			
			$data_url = trim($_POST['pt-stimm-gremium-ext-url']);
			
			if ($data_url == "") $error[] = "URL ungültig.";
			
			if (count($error) == 0) {
				$options['gremien-ext'][$newID]['url'] = $_POST['pt-stimm-gremium-ext-url'];
				$options['gremien-ext'][$newID]['data'] = "";
				$options['gremien-ext'][$newID]['datadate'] = 0;
				update_option("pt_stimm", $options);
				$success[] = "Externes Gremium hinzugefügt.";
			}
			$page = "home";
		}
		
		if ($_POST['pt-stimm-action'] == "gremium-ext-del") {
			$delID = $_POST['pt-stimm-gremium-id'];
			
			if (!$options['gremien-ext'][$delID]) $error[] = "Fehler.";
			
			if (count($error) == 0) {
				unset($options['gremien-ext'][$delID]);
				update_option("pt_stimm", $options);
				$success[] = "Externes Gremium gelöscht.";
			}
			
			$page = "home";
			
		}
		
		if ($_POST['pt-stimm-action'] == "mitglied-add") {
			$gremiumid = $_POST['pt-stimm-gremium-id'];
			$data_name = htmlspecialchars(trim(stripslashes($_POST['pt-stimm-mitglied-name'])));
			$data_kuerzel = htmlspecialchars(trim(stripslashes($_POST['pt-stimm-mitglied-kuerzel'])));
			$data_partei = htmlspecialchars(trim(stripslashes($_POST['pt-stimm-mitglied-partei'])));
			
			if (!$options['gremien'][$gremiumid]) $error[] = "Fehler.";
			if ($data_name == "") $error[] = "Es muss ein Name angegeben werden.";
			if ($data_kuerzel == "") $error[] = "Es muss ein Kürzel angegeben werden.";
			
			if (count($error) == 0) {
				$options['gremien'][$gremiumid]->addMitglied($data_name, $data_kuerzel, $data_partei);
				update_option("pt_stimm", $options);
				$success[] = "Mitglied hinzugefügt.";
			}
			$page = "gremium-edit";
		}
		
		if ($_POST['pt-stimm-action'] == "mitglied-edit") {
			$gremiumid = $_POST['pt-stimm-gremium-id'];
			$mitgliedid = $_POST['pt-stimm-mitglied-id'];
			$data_name = htmlspecialchars(trim(stripslashes($_POST['pt-stimm-mitglied-name'])));
			$data_kuerzel = htmlspecialchars(trim(stripslashes($_POST['pt-stimm-mitglied-kuerzel'])));
			$data_partei = htmlspecialchars(trim(stripslashes($_POST['pt-stimm-mitglied-partei'])));
			
			if (!$options['gremien'][$gremiumid]) $error[] = "Fehler.";
			if (!$options['gremien'][$gremiumid]->mitglieder[$mitgliedid]) $error[] = "Fehler.";
			if ($data_name == "") $error[] = "Es muss ein Name angegeben werden.";
			if ($data_kuerzel == "") $error[] = "Es muss ein Kürzel angegeben werden.";
			
			if (count($error) == 0) {
				$options['gremien'][$gremiumid]->mitglieder[$mitgliedid]->name = $data_name;
				$options['gremien'][$gremiumid]->mitglieder[$mitgliedid]->kuerzel = $data_kuerzel;
				$options['gremien'][$gremiumid]->mitglieder[$mitgliedid]->partei = $data_partei;
				update_option("pt_stimm", $options);
				$success[] = "Mitglied bearbeitet.";
			}
			$page = "gremium-edit";
		}
		
		if ($_POST['pt-stimm-action'] == "mitglied-del") {
			$gremiumid = $_POST['pt-stimm-gremium-id'];
			$delID = $_POST['pt-stimm-mitglied-id'];
			
			if (!$options['gremien'][$gremiumid]) $error[] = "Fehler.";
			
			if ($options['gremien'][$gremiumid]->mitglieder[$delID]) {
				unset($options['gremien'][$gremiumid]->mitglieder[$delID]);
				update_option("pt_stimm", $options);
				$success[] = "Mitglied gelöscht.";
			} else {
				$error[] = "Mitglied konnte nicht gelöscht werden.";
			}
			
			
			$page="gremium-edit";
		}
		
		if ($_POST['pt-stimm-action'] == "sitzung-add") {
			$gremiumid = $_POST['pt-stimm-gremium-id'];
			$data_name = htmlspecialchars(trim(stripslashes($_POST['pt-stimm-sitzung-name'])));
			$data_datum = strtotime($_POST['pt-stimm-sitzung-datum']);
			$data_linkzurto = trim($_POST['pt-stimm-sitzung-linkzurto']);
			
			if (!$options['gremien'][$gremiumid]) $error[] = "Fehler.";
			if ($data_name == "") $error[] = "Es muss ein Name angegeben werden.";
			if ($data_datum == false) $error[] = "Es muss ein korrektes Datum angegeben werden.";
			
			if (count($error) == 0) {
				$options['gremien'][$gremiumid]->addSitzung($data_name, $data_datum, $data_linkzurto);
				update_option("pt_stimm", $options);
				$success[] = "Sitzung hinzugefügt.";
			}
			$page="gremium-edit";
		}
		
		if ($_POST['pt-stimm-action'] == "sitzung-edit") {
			$gremiumid = $_POST['pt-stimm-gremium-id'];
			$sitzungid = $_POST['pt-stimm-sitzung-id'];
			
			$data_name = htmlspecialchars(trim(stripslashes($_POST['pt-stimm-sitzung-name'])));
			$data_datum = strtotime($_POST['pt-stimm-sitzung-datum']);
			$data_linkzurto = trim($_POST['pt-stimm-sitzung-linkzurto']);
			
			if (!$options['gremien'][$gremiumid]) $error[] = "Fehler.";
			if (!$options['gremien'][$gremiumid]->sitzungen[$sitzungid]) $error[] = "Fehler.";
			if ($data_name == "") $error[] = "Es muss ein Name angegeben werden.";
			if ($data_datum == false) $error[] = "Es muss ein korrektes Datum angegeben werden.";
			
			if (count($error) == 0) {
				$options['gremien'][$gremiumid]->sitzungen[$sitzungid]->name = $data_name;
				$options['gremien'][$gremiumid]->sitzungen[$sitzungid]->datum = $data_datum;
				$options['gremien'][$gremiumid]->sitzungen[$sitzungid]->linkzurto = $data_linkzurto;
				update_option("pt_stimm", $options);
				$success[] = "Sitzung bearbeitet.";
			}
			$page="sitzung-edit";
		}
		
		if ($_POST['pt-stimm-action'] == "sitzung-del") {
			$gremiumid = $_POST['pt-stimm-gremium-id'];
			$delID = $_POST['pt-stimm-sitzung-id'];
			
			if ($options['gremien'][$gremiumid]->sitzungen[$delID]) {
				$datum = $options['gremien'][$gremiumid]->sitzungen[$delID]->datum;
				unset($options['gremien'][$gremiumid]->sitzungenByDatum[$datum][$delID]);
				unset($options['gremien'][$gremiumid]->sitzungen[$delID]);
				update_option("pt_stimm", $options);
				$success[] = "Sitzung gelöscht.";
			} else {
				$error[] = "Sitzung konnte nicht gelöscht werden.";
			}
			$page="gremium-edit";
		}
		
		if ($_POST['pt-stimm-action'] == "punkt-add") {
			$gremiumid = $_POST['pt-stimm-gremium-id'];
			$sitzungid = $_POST['pt-stimm-sitzung-id'];
			
			$data_text = trim(stripslashes($_POST['pt-stimm-punkt-text']));
			$data_rislink = trim($_POST['pt-stimm-punkt-rislink']);

			if (!$options['gremien'][$gremiumid]) $error[] = "Fehler.";
			if (!$options['gremien'][$gremiumid]->sitzungen[$sitzungid]) $error[] = "Fehler.";
			if ($data_text == "") $error[] = "Es muss ein Text angegeben werden.";
			
			if (count($error) == 0) {
				$options['gremien'][$gremiumid]->sitzungen[$sitzungid]->addPunkt($data_text, $data_rislink);
				update_option("pt_stimm", $options);
				$success[] = "Punkt hinzugefügt.";
			}
			$page="sitzung-edit";
		}
		
		if ($_POST['pt-stimm-action'] == "punkt-edit") {
			$gremiumid = $_POST['pt-stimm-gremium-id'];
			$sitzungid = $_POST['pt-stimm-sitzung-id'];
			$punktid = $_POST['pt-stimm-punkt-id'];
			
			$data_text = trim(stripslashes($_POST['pt-stimm-punkt-text']));
			$data_rislink = trim($_POST['pt-stimm-punkt-rislink']);

			if (!$options['gremien'][$gremiumid]) $error[] = "Fehler.";
			if (!$options['gremien'][$gremiumid]->sitzungen[$sitzungid]) $error[] = "Fehler.";
			if (!$options['gremien'][$gremiumid]->sitzungen[$sitzungid]->punkte[$punktid]) $error[] = "Fehler.";
			if ($data_text == "") $error[] = "Es muss ein Text angegeben werden.";
			
			if (count($error) == 0) {
				$options['gremien'][$gremiumid]->sitzungen[$sitzungid]->punkte[$punktid]->text = $data_text;
				$options['gremien'][$gremiumid]->sitzungen[$sitzungid]->punkte[$punktid]->rislink = $data_rislink;
				update_option("pt_stimm", $options);
				$success[] = "Punkt bearbeitet.";
			}
			$page="sitzung-edit";
		}
		

		
		if ($_POST['pt-stimm-action'] == "stimmen-edit") {
			$gremiumid = $_POST['pt-stimm-gremium-id'];
			$sitzungid = $_POST['pt-stimm-sitzung-id'];
			if (isset($_POST['pt-stimm-punkt-del'])) {
				$delID = $_POST['pt-stimm-punkt-del'];
				if ($options['gremien'][$gremiumid]->sitzungen[$sitzungid]->punkte[$delID]) {
					unset($options['gremien'][$gremiumid]->sitzungen[$sitzungid]->punkte[$delID]);
					update_option("pt_stimm", $options);
					$success[] = "Punkt gelöscht.";
				} else {
					$error[] = "Fehler";
				}
				$page="sitzung-edit";
			} elseif (isset($_POST['pt-stimm-punkt-edit'])) {
				$page = "punkt-edit";
			} else {
				$values = $_POST['pt-stimm-stimmen'];
				foreach ($values as $key => $val) {
					if ($options['gremien'][$gremiumid]->sitzungen[$sitzungid]->punkte[$key]) {
						foreach ($val as $key2 => $val2) {
							if ($options['gremien'][$gremiumid]->mitglieder[$key2]) {
								$options['gremien'][$gremiumid]->sitzungen[$sitzungid]->punkte[$key]->editStimme($options['gremien'][$gremiumid]->mitglieder[$key2], $val2);
							} else {
								$error[] = "Fehler.";
								break;
							}
						}
					} else {
						break;
						$error[] = "Fehler.";
					}
				}
				if (count($error) == 0) {
					update_option("pt_stimm", $options);
					$success[] = "Stimmverhalten geändert.";
				} else {
					$options = get_option("pt_stimm");
				}
				$page="sitzung-edit";
			}
			//$options['gremien'][$gremiumid]->addPunkt($_POST['pt-stimm-punkt-text'], strtotime($_POST['pt-stimm-punkt-datum']));
			
			
		}
		
		if ($_POST['pt-stimm-action'] == "toimport") {
			$gremiumid = $_POST['pt-stimm-gremium-id'];
			$sitzungid = $_POST['pt-stimm-sitzung-id'];
			
			if (!$options['gremien'][$gremiumid]) $error[] = "Fehler.";
			if (!$options['gremien'][$gremiumid]->sitzungen[$sitzungid]) $error[] = "Fehler.";
			
			$url = trim($_POST['pt-stimm-toimport']);
			
			$data = PT_stimm::getRISTO($url);
			if ((count($error) == 0) && $data && is_array($data) && (count($data)>0)) {
				
				foreach ($data as $key => $val) {
					$data_text = trim($val['name']);
					$data_rislink = trim($val['link']);

					
					if ($data_text == "") $error[] = "Es muss ein Text angegeben werden.";
					
					if (count($error) == 0) {
						$options['gremien'][$gremiumid]->sitzungen[$sitzungid]->addPunkt($data_text, $data_rislink);
					} else {
						$error[] = "Fehler.";
						break;
					}
				}
				if (count($error) == 0) {
					update_option("pt_stimm", $options);
					$success[] = "TO hinzugefügt.";
				}
			
			} else {
				$error[] = "Die angegebene URL konnte nicht analysiert werden.";
			}
			

			$page="sitzung-edit";
		}
		
		if (count($success) > 0) {
			foreach ($success as $val) {
				?>
				<div class="hinweis updated">
				<?php echo $val; ?>
				</div>
				<?php
			}
		}
		if (count($error) > 0) {
			foreach ($error as $val) {
				?>
				<div class="hinweis error">
				<?php echo $val; ?>
				</div>
				<?php
			}
		}
			
		if($page == "home") {
			include('admin/home.php');
		}
		if($page == "gremium-edit") {
			$gremiumid = $_POST['pt-stimm-gremium-id'];
			$gremium = &$options['gremien'][$gremiumid];
			include('admin/gremium-edit.php');
		}
		if($page == "sitzung-edit") {
			$gremiumid = $_POST['pt-stimm-gremium-id'];
			$sitzungid = $_POST['pt-stimm-sitzung-id'];
			$gremium = &$options['gremien'][$gremiumid];
			$sitzung = &$options['gremien'][$gremiumid]->sitzungen[$sitzungid];
			include('admin/sitzung-edit.php');
		}
		
		if($page == "mitglied-edit") {
			$gremiumid = $_POST['pt-stimm-gremium-id'];
			$mitgliedid = $_POST['pt-stimm-mitglied-id'];
			$gremium = &$options['gremien'][$gremiumid];
			$mitglied = &$options['gremien'][$gremiumid]->mitglieder[$mitgliedid];
			include('admin/mitglied-edit.php');
		}
		
		if($page == "punkt-edit") {
			$gremiumid = $_POST['pt-stimm-gremium-id'];
			$sitzungid = $_POST['pt-stimm-sitzung-id'];
			$punktid = $_POST['pt-stimm-punkt-edit'];
			$gremium = &$options['gremien'][$gremiumid];
			$sitzung = &$options['gremien'][$gremiumid]->sitzungen[$sitzungid];
			$punkt = &$options['gremien'][$gremiumid]->sitzungen[$sitzungid]->punkte[$punktid];
			include('admin/punkt-edit.php');
		}
		
		//echo "<pre>"; print_r($options); echo "</pre>"; 
		//$test0 = JSON_encode($options['gremien'][10]);
		//$test1 = JSON_decode($test0);
		//echo "<pre>"; print_r($test1); echo "</pre>"; 
		//$options['gremien'][10] = $test1;
		//echo "<hr>";
		//echo "<pre>"; print_r($options); echo "</pre>"; 
	}

	
}

add_shortcode( "pt-stimm", array("PT_stimm", "shortcode"));
add_shortcode( "pt-stimm-legende", array("PT_stimm", "shortcode_legende"));
?>