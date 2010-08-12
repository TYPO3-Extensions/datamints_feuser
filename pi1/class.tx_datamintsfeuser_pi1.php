<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Bernhard Baumgartl <b.baumgartl@datamints.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *   91: class tx_datamintsfeuser_pi1 extends tslib_pibase
 *  115:     function main($content, $conf)
 *  174:     function sendForm()
 *  416:     function uniqueCheckForm()
 *  446:     function validateForm()
 *  589:     function requireCheckForm()
 *  609:     function generatePassword($fieldName)
 *  667:     function generatePasswordForMail($userId)
 *  686:     function checkPassword($submitedPassword, $originalPassword)
 *  735:     function saveDeleteImage($fieldName, &$arrUpdate)
 *  818:     function showMessageOutputRedirect($mode, $submode = '', $params = array())
 *  886:     function userAutoLogin($username, $mode = '')
 *  903:     function userRedirect($pageId = 0)
 *  920:     function sendActivationMail($userId)
 *  956:     function makeApprovalCheck($userId)
 * 1016:     function getApprovalTypes()
 * 1028:     function isAdminMail($approvalType)
 * 1038:     function setNotActivatedCookie($userId)
 * 1050:     function getNotActivatedUserArray($arrNotActivated = array())
 * 1084:     function sendMail($userId, $templatePart, $adminMail = true, $extraMarkers = array())
 * 1145:     function getTemplateSubpart($templatePart, $markerArray = array())
 * 1172:     function showForm($valueCheck = array())
 * 1379:     function cleanSpecialFieldKey($fieldName)
 * 1394:     function showInput($fieldName, $arrCurrentData, $iItem)
 * 1433:     function showText($fieldName, $arrCurrentData)
 * 1449:     function showCheck($fieldName, $arrCurrentData)
 * 1465:     function showSelect($fieldName, $arrCurrentData)
 * 1515:     function showGroup($fieldName, $arrCurrentData)
 * 1563:     function makeHiddenFields()
 * 1580:     function makeHiddenParams()
 * 1598:     function cleanHeaderUrlData($data)
 * 1610:     function checkIfRequired($fieldName)
 * 1624:     function getLabel($fieldName)
 * 1667:     function getErrorLabel($fieldName, $valueCheck)
 * 1683:     function getDefaultLanguage()
 * 1701:     function getConfiguration()
 * 1746:     function readFlexformTab($flexData, &$conf, $sTab)
 * 1780:     function setFlexformConfiguration($key, $value)
 * 1808:     function setIrreConfiguration()
 * 1937:     function getJSValidationConfiguration()
 * 2033:     function getFeUsersTca()
 * 2047:     function getStoragePid()
 * 2061:     function deletePointInArrayKey($array)
 * 2092:     function checkUtf8($str)
 * 2136:     function cleanArray($array)
 *
 *
 * TOTAL FUNCTIONS: 44
 *
 */

require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Plugin 'Frontend User Management' for the 'datamints_feuser' extension.
 *
 * @author	Bernhard Baumgartl <b.baumgartl@datamints.com>
 * @package	TYPO3
 * @subpackage	tx_datamintsfeuser
 */
class tx_datamintsfeuser_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_datamintsfeuser_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_datamintsfeuser_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'datamints_feuser';	// The extension key.
	var $pi_checkCHash = true;
	var $feUsersTca = array();
	var $storagePid = 0;
	var $contentUid = 0;
	var $conf = array();
	var $extConf = array();
	var $lang = array();
	var $userId = 0;
	var $arrUsedFields = array();
	var $arrRequiredFields = array();
	var $arrUniqueFields = array();
	var $arrHiddenParams = array();

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	string		The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;

		// Debug.
		//$GLOBALS['TSFE']->set_no_cache();
		//$GLOBALS['TYPO3_DB']->debugOutput = true;

		// ContentId ermitteln.
		$this->contentId = $this->cObj->data['uid'];

		// UserId ermitteln.
		$this->userId = $GLOBALS['TSFE']->fe_user->user['uid'];

		// Flexform und Configurationen laden.
		$this->pi_setPiVarDefaults();
		$this->pi_initPIflexForm();
		$this->getConfiguration();
		$this->getStoragePid();
		$this->getFeUsersTca();
		$this->pi_loadLL();

		// Stylesheets in den Head einbinden.
		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId . '[stylesheet]'] = ($this->conf['disablestylesheet']) ? '' : '<link rel="stylesheet" type="text/css" href="' . (($this->conf['stylesheetpath']) ? $this->conf['stylesheetpath'] : t3lib_extMgm::extRelPath($this->extKey) . 'res/datamints_feuser.css') . '" />';

		// Javascripts in den Head einbinden.
		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId . '[jsvalidator]'] = ($this->conf['disablejsvalidator']) ? '' : '<script type="text/javascript" src="' . (($this->conf['jsvalidatorpath']) ? $this->conf['jsvalidatorpath'] : t3lib_extMgm::extRelPath($this->extKey) . 'res/validator.min.js') . '"></script>';
		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId . '[jsvalidation][' . $this->contentId . ']'] = '<script type="text/javascript">' . "\n/*<![CDATA[*/\n" . $this->getJSValidationConfiguration() . "\n/*]]>*/\n" . '</script>';

		// Wenn nicht eingeloggt kann man auch nicht editieren!
		if ($this->conf['showtype'] == 'edit' && !$this->userId) {
			return $this->pi_wrapInBaseClass($this->showMessageOutputRedirect('edit_error_no_login'));
		}

		switch ($this->piVars[$this->contentId]['submit']) {

			case 'send':
				$content = $this->sendForm();
				break;

			case 'approvalcheck':
				// Userid ermittln und Aktivierung durchfuehren.
				$userId = intval($this->piVars[$this->contentId]['uid']);
				$content = $this->makeApprovalCheck($userId);
				break;

			default:
				$content = $this->showForm();
				break;

		}

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Bereitet die uebergebenen Daten fuer den Import in die Datenbank vor, und fuehrt diesen, wenn es keine Fehler gab, aus.
	 *
	 * @return	string
	 */
	function sendForm() {
		$mode = '';
		$submode = '';
		$params = array();

		// Jedes Element trimmen.
		foreach ($this->piVars[$this->contentId] as $key => $value) {
			if (!is_array($value)) {
				$this->piVars[$this->contentId][$key] = trim($value);
			}
		}

		// Ueberpruefen ob Datenbankeintraege mit den uebergebenen Daten uebereinstimmen.
		$uniqueCheck = $this->uniqueCheckForm();

		// Eine Validierung durchfuehren ueber alle Felder die eine gesonderte Konfigurtion bekommen haben.
		$validCheck = $this->validateForm();

		// Ueberpruefen ob in allen benoetigten Feldern etwas drinn steht.
		$requireCheck = $this->requireCheckForm();

		// Wenn bei der Validierung ein Feld nicht den Anforderungen entspricht noch einmal die Form anzeigen und entsprechende Felder markieren.
		$valueCheck = array_merge((array)$uniqueCheck, (array)$validCheck, (array)$requireCheck);

		if (count($valueCheck) > 0) {
			return $this->showForm($valueCheck);
		}

		// Temporaeren Feldnamen fuer das 'Aktivierungslink zusenden' Feld erstellen.
		$fieldName = '--resendactivation--';

		// Wenn der User eine neue Aktivierungsmail beantragt hat.
		if ($this->piVars[$this->contentId][$this->cleanSpecialFieldKey($fieldName)] && in_array($fieldName, $this->arrUsedFields)) {
			$fieldName = $this->cleanSpecialFieldKey($fieldName);

			// Falls der Anzeigetyp "list" (List der im Cookie gespeicherten User), jeden uebergebenen User saeubern und alle ermitteln, ansonsten den einzelnen betroffenen User ermitteln.
			//if ($this->conf['shownotactivated'] == 'list') {
			//	$arrNotActivated = $this->getNotActivatedUserArray($this->piVars[$this->contentId][$fieldName]);
			//	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, tx_datamintsfeuser_approval_level', 'fe_users', 'pid = ' . intval($this->storagePid) . ' AND uid IN(' . implode(',', $arrNotActivated) . ') AND disable = 1 AND deleted = 0');
			//} else {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, tx_datamintsfeuser_approval_level', 'fe_users', 'pid = ' . intval($this->storagePid) . ' AND email = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(strtolower($this->piVars[$this->contentId][$fieldName]), 'fe_users') . ' AND disable = 1 AND deleted = 0', '', '', '1');
			//}

			//while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				// Genehmigungstypen aufsteigend sortiert ermitteln. Das ist nötig um das Level dem richtigen Typ zuordnen zu können.
				// Beispiel: approvalcheck = ,doubleoptin,adminapproval => beim exploden kommt dann ein leeres Arrayelement herraus, das nach dem entfernen einen leeren Platz uebrig lässt.
				$arrApprovalTypes = $this->getApprovalTypes();
				$approvalType = $arrApprovalTypes[count($arrApprovalTypes) - $row['tx_datamintsfeuser_approval_level']];

				// Ausgabe vorbereiten.
				$mode = $fieldName;

				// Fehler anzeigen, falls das naechste aktuelle Genehmigungsverfahren den Admin betrifft.
				$submode = 'failure';

				// Aktivierungsmail senden und Ausgabe anpassen.
				if ($approvalType && !$this->isAdminMail($approvalType)) {
					$this->sendActivationMail($row['uid']);
					$submode = 'sent';
				}
			//}

			return $this->showMessageOutputRedirect($mode, $submode);
		}

		// Wenn der Bearbeitungsmodus, die Zielseite, und der User stimmen, dann wird in die Datenbank geschrieben.
		if ($this->piVars[$this->contentId]['submitmode'] == $this->conf['showtype'] && $this->piVars[$this->contentId]['pageid'] == $GLOBALS['TSFE']->id && $this->piVars[$this->contentId]['userid'] == $this->userId) {
			// Sonderfaelle!
			foreach ($this->arrUsedFields as $fieldName) {
				if ($this->feUsersTca['columns'][$fieldName]) {
					// Ist das Feld schon gesaeubert worden (MySQL, PHP, HTML, ...).
					$isChecked = false;

					// Passwordfelder behandeln.
					if (strpos($this->feUsersTca['columns'][$fieldName]['config']['eval'], 'password') !== false) {
						// Password generieren und verschluesseln je nach Einstellung.
						$password = $this->generatePassword($fieldName);
						$arrUpdate[$fieldName] = $password['encrypted'];

						// Wenn kein Password uebergeben wurde auch keins schreiben.
						if (!$arrUpdate[$fieldName]) {
							unset($arrUpdate[$fieldName]);
						}

						$isChecked = true;
					}

					// Bildfelder behandeln.
					if ($this->feUsersTca['columns'][$fieldName]['config']['internal_type'] == 'file' && ($_FILES[$this->prefixId]['type'][$this->contentId][$fieldName] || $this->piVars[$this->contentId][$fieldName . '_delete'])) {
						// Das Bild hochladen oder loeschen. Gibt einen Fehlerstring zurueck falls ein Fehler auftritt. $arrUpdate wird per Referenz uebergeben und innerhalb der Funktion geaendert!
						$valueCheck[$fieldName] = $this->saveDeleteImage($fieldName, $arrUpdate);

						if ($valueCheck[$fieldName]) {
							return $this->showForm($valueCheck);
						}

						$isChecked = true;
					}

					// Checkboxen behandeln.
					if ($this->feUsersTca['columns'][$fieldName]['config']['type'] == 'check' && !$this->piVars[$this->contentId][$fieldName]) {
						$arrUpdate[$fieldName] = '0';
					}

					// Datumsfelder behandeln.
					if (strpos($this->feUsersTca['columns'][$fieldName]['config']['eval'], 'date') !== false) {
						$arrUpdate[$fieldName] = strtotime($this->piVars[$this->contentId][$fieldName]);
						$isChecked = true;
					}

					// Multiple Selectboxen.
					if ($this->feUsersTca['columns'][$fieldName]['config']['type'] == 'select' && $this->feUsersTca['columns'][$fieldName]['config']['size'] > 1) {
						foreach ($this->piVars[$this->contentId][$fieldName] as $key => $val) {
							$this->piVars[$this->contentId][$fieldName][$key] = intval($val);
						}

						$arrUpdate[$fieldName] = implode(',', $this->piVars[$this->contentId][$fieldName]);
						$isChecked = true;
					}

					// Wenn noch nicht gesaeubert dann nachholen!
					if (!$isChecked && isset($this->piVars[$this->contentId][$fieldName])) {
						// Typ ermitteln und anhand dessen das Feld saeubern.
						$type = $this->feUsersTca['columns'][$fieldName]['config']['type'];
						$size = $this->feUsersTca['columns'][$fieldName]['config']['size'];

						// Wenn eine Checkbox oder eine einfache Selectbox, dann darf nur eine Zahl kommen!
						if ($type == 'check' || ($type == 'select' && $size == 1)) {
							$arrUpdate[$fieldName] = intval($this->piVars[$this->contentId][$fieldName]);
						}

						// Ansonsten Standardsaeuberung.
						$arrUpdate[$fieldName] = strip_tags($this->piVars[$this->contentId][$fieldName]);

						// Wenn E-Mail Feld, alle Zeichen zu kleinen Zeichen konvertieren.
						if ($fieldName == 'email') {
							$arrUpdate[$fieldName] = strtolower($arrUpdate[$fieldName]);
						}
					}
				}
			}

			// Zusatzfelder setzten, die nicht aus der Form uebergeben wurden.
			$arrUpdate['tstamp'] = time();

			// Konvertiert alle moeglichen Zeichen die fuer die Ausgabe angepasst wurden zurueck.
			foreach ($arrUpdate as $key => $val) {
				$arrUpdate[$key] = htmlspecialchars_decode($val);
			}

			// Temporaeren Feldnamen fuer das 'User loeschen' Feld erstellen.
			$fieldName = '--userdelete--';

			// Wenn der User geloescht werden soll.
			if ($this->piVars[$this->contentId][$this->cleanSpecialFieldKey($fieldName)] && in_array($fieldName, $this->arrUsedFields)) {
				$arrUpdate['deleted'] = '1';
			}

			// Der User hat seine Daten editiert.
			if ($this->conf['showtype'] == 'edit') {
				// User editieren.
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $this->userId , $arrUpdate);

				// Ausgabe vorbereiten.
				$mode = $this->conf['showtype'];
				$submode = 'success';

				// Wenn der User geloescht wurde, weiterleiten.
				if ($arrUpdate['deleted']) {
					$mode = 'userdelete';
				}
			}

			// Ein neuer User hat sich angemeldet.
			if ($this->conf['showtype'] == 'register') {
				// Standartkonfigurationen anwenden.
				$arrUpdate['pid'] = $this->storagePid;
				$arrUpdate['usergroup'] = ($arrUpdate['usergroup']) ? $arrUpdate['usergroup'] : $this->conf['register.']['usergroup'];
				$arrUpdate['crdate'] = $arrUpdate['tstamp'];

				// Extra Erstellungsdatumsfelder hinzufuegen.
				$arrCrdateFields = $this->cleanArray(t3lib_div::trimExplode(',', $this->conf['register.']['crdatefields']));

				foreach ($arrCrdateFields as $val) {
					if (trim($val)) {
						$arrUpdate[trim($val)] = $arrUpdate['crdate'];
					}
				}

				// Genehmigungstypen aufsteigend sortiert ermitteln. Das ist nötig um das Level dem richtigen Typ zuordnen zu können.
				// Beispiel: approvalcheck = ,doubleoptin,adminapproval => beim exploden kommt dann ein leeres Arrayelement herraus, das nach dem entfernen einen leeren Platz uebrig lässt.
				$arrApprovalTypes = $this->getApprovalTypes();
				$approvalType = $arrApprovalTypes[0];

				// Maximales Genehmigungslevel ermitteln (Double Opt In / Admin Approval).
				$arrUpdate['tx_datamintsfeuser_approval_level'] = count($arrApprovalTypes);

				// Wenn ein Genehmigungstyp aktiviert ist, dann den User deaktivieren.
				if ($arrUpdate['tx_datamintsfeuser_approval_level'] > 0) {
					$arrUpdate['disable'] = '1';
				}

				// User erstellen.
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_users', $arrUpdate);

				// Userid ermittln un Global definieren!
				$userId = $GLOBALS['TYPO3_DB']->sql_insert_id();

				// Wenn nach der Registrierung weitergeleitet werden soll.
				if ($arrUpdate['tx_datamintsfeuser_approval_level'] > 0) {
					// Aktivierungsmail senden.
					$this->sendActivationMail($userId);

					// Ausgabe fuer gemischte Genehmigungstypen erstellen (z.B. erst adminapproval und dann doubleoptin).
					$mode = $approvalType;
					$submode = implode('_', array_shift($arrApprovalTypes));
					$submode .= ($submode) ? '_sent' : 'sent';
					$params = array('mode' => 'register');
				} else {
					// Erstellt ein neues Passwort, falls Passwort generieren eingestellt ist. Das Passwort kannn dann ueber den Marker "###PASSWORD###" mit der Registrierungsmail gesendet werden.
					$extraMarkers = $this->generatePasswordForMail($userId);

					// Registrierungsemail schicken.
					$this->sendMail($userId, 'registration', true);
					$this->sendMail($userId, 'registration', false, $extraMarkers);

					$mode = $this->conf['showtype'];
					$submode = 'success';
					$params = array('username' => $arrUpdate['username']);
				}
			}
		}

		return $this->showMessageOutputRedirect($mode, $submode, $params);
	}

	/**
	 * Ueberprueft die uebergebenen Inhalte, bei bestimmten Feldern, ob diese in der Datenbank schon vorhanden sind.
	 *
	 * @return	array		$valueCheck
	 */
	function uniqueCheckForm() {
		$valueCheck = array();

		// Check unique Fields.
		$arrUniqueFields = $this->cleanArray(t3lib_div::trimExplode(',', $this->conf['uniquefields']));

		// Wenn User eingeloggt, dann den eigenen Datensatz nicht durchsuchen.
		if ($this->conf['showtype'] == 'edit' && $this->userId) {
			$where = ' AND uid <> ' . $this->userId;
		}

		foreach ($arrUniqueFields as $fieldName) {
			if ($this->piVars[$this->contentId][$fieldName]) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(uid) as count', 'fe_users', 'pid = ' . intval($this->storagePid) . ' AND ' . $fieldName . ' = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($this->piVars[$this->contentId][$fieldName], 'fe_users') . $where . ' AND deleted = 0');
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

				if ($row['count'] >= 1) {
					$valueCheck[$fieldName] = 'unique';
				}
			}
		}

		return $valueCheck;
	}

	/**
	 * Ueberprueft ob alle Validierungen eingehalten wurden.
	 *
	 * @return	array		$valueCheck
	 */
	function validateForm() {
		$valueCheck = array();

		// Alle ausgewaehlten Felder durchgehen.
		foreach ($this->arrUsedFields as $fieldName) {
			 $validate = $this->conf['validate.'][$fieldName . '.'];

			// Wenn der im TypoScript angegebene Feldname existiert,
			if ($this->feUsersTca['columns'][$fieldName]
					// ein Wert uebergeben wurde,
					&& $this->piVars[$this->contentId][$fieldName]
					// der Konfigurierte Modus stimmt,
					&& (!$validate['mode'] || $validate['mode'] == $this->conf['showtype'])
					// und das Feld ueberhaupt angezeigt wurde, dann validieren.
					&& in_array($fieldName, $this->arrUsedFields)) {

				$value = $this->piVars[$this->contentId][$fieldName];

				switch ($validate['type']) {

					case 'password':
						$value_rep = $this->piVars[$this->contentId][$fieldName . '_rep'];
						$arrLength[0] = 6;

						if ($value == $value_rep) {
							if ($validate['length']) {
								$arrLength = t3lib_div::trimExplode(',', $validate['length']);

								if ($arrLength[1]) {
									// Wenn eine Maximallaenge festgelegt wurde.
									if (strlen($value) < $arrLength[0] || strlen($value) > $arrLength[1]) {
										$valueCheck[$fieldName] = 'length';
									}
								} else {
									// Wenn nur eine Minimallaenge festgelegt wurde.
									if (strlen($value) < $arrLength[0]) {
										$valueCheck[$fieldName] = 'length';
									}
								}
							} else {
								// Wenn nur eine Minimallaenge festgelegt wurde.
								if (strlen($value) < $arrLength[0]) {
									$valueCheck[$fieldName] = 'length';
								}
							}
						} else {
							$valueCheck[$fieldName] = 'equal';
						}
						break;

					case 'email':
						if (!preg_match('/^[a-zA-Z0-9\._%+-]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,6}$/', $value)) {
							$valueCheck[$fieldName] = 'valid';
						}
						break;

					case 'username':
						if (!preg_match('/^[^ ]*$/', $value)) {
							$valueCheck[$fieldName] = 'valid';
						}
						break;

					case 'custom':
						if ($validate['regexp']) {
							if (is_array($value)) {
								foreach ($value as $subValue) {
									if (!preg_match($validate['regexp'], $subValue)) {
										$valueCheck[$fieldName] = 'valid';
									}
								}
							} else {
								if (!preg_match($validate['regexp'], $value)) {
									$valueCheck[$fieldName] = 'valid';
								}
							}
						}
						if ($validate['length']) {
							$arrLength = t3lib_div::trimExplode(',', $validate['length']);

							if (is_array($value)) {
								if ($arrLength[1]) {
									// Wenn eine Maximallaenge festgelegt wurde.
									if (count($value) < $arrLength[0] || count($value) > $arrLength[1]) {
										$valueCheck[$fieldName] = 'length';
									}
								} else {
									// Wenn nur eine Minimallaenge festgelegt wurde.
									if (count($value) < $arrLength[0]) {
										$valueCheck[$fieldName] = 'length';
									}
								}
							} else {
								if ($arrLength[1]) {
									// Wenn eine Maximallaenge festgelegt wurde.
									if (strlen($value) < $arrLength[0] || strlen($value) > $arrLength[1]) {
										$valueCheck[$fieldName] = 'length';
									}
								} else {
									// Wenn nur eine Minimallaenge festgelegt wurde.
									if (strlen($value) < $arrLength[0]) {
										$valueCheck[$fieldName] = 'length';
									}
								}
							}
						}
						break;

				}
			}

			// Besonderes Feld das fest in der Extension verbaut ist (password_confirmation), und ueberprueft werden soll.
			if ($fieldName == '--passwordconfirmation--' && $this->conf['showtype'] == 'edit' && $this->userId) {
				$fieldName = $this->cleanSpecialFieldKey($fieldName);

				if (!$this->checkPassword($this->piVars[$this->contentId][$fieldName], $GLOBALS['TSFE']->fe_user->user['password'])) {
					$valueCheck[$fieldName] = 'valid';
				}
			}

			// Besonderes Feld das fest in der Extension verbaut ist (resend_activation), und ueberprueft werden soll.
			if ($fieldName == '--resendactivation--') {
				$fieldName = $this->cleanSpecialFieldKey($fieldName);

				if ($this->piVars[$this->contentId][$fieldName]) {
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(uid) as count', 'fe_users', 'pid = ' . intval($this->storagePid) . ' AND (uid = ' . intval($this->piVars[$this->contentId][$fieldName]) . ' OR email = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(strtolower($this->piVars[$this->contentId][$fieldName]), 'fe_users') . ') AND disable = 1 AND deleted = 0');
					$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

					if ($row['count'] < 1) {
						$valueCheck[$fieldName] = 'valid';
					}
				}
			}

		}

		return $valueCheck;
	}

	/**
	 * Ueberprueft ob alle benoetigten Felder mit Inhalten uebergeben wurden.
	 *
	 * @return	array		$valueCheck
	 */
	function requireCheckForm() {
		$valueCheck = array();

		// Geht alle benoetigten Felder durch und ermittelt fehlende.
		foreach ($this->arrRequiredFields as $fieldName) {
			// Ueberpruefen, ob das Feld ueberhaupt benoetigt wird, und ob ein Wert uebergeben wurde.
			if (in_array($fieldName, $this->arrUsedFields) && !$this->piVars[$this->contentId][$this->cleanSpecialFieldKey($fieldName)]) {
				$valueCheck[$fieldName] = 'required';
			}
		}

		return $valueCheck;
	}

	/**
	 * Erstellt wenn gefordert ein Password, und verschluesselt dieses, oder das uebergebene, wenn es verschluesselt werden soll.
	 *
	 * @param	string		$fieldName
	 * @return	array		$password
	 */
	function generatePassword($fieldName) {
		$password = array();

		// Uebergebenes Password setzten.
		$password['normal'] = $this->piVars[$this->contentId][$fieldName];

		// Erstellt ein Password.
		if ($this->conf['register.']['generatepassword.']['mode']) {
			$i = 1;
			$password['normal'] = '';
			$chars = '234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

			while ($i <= $this->conf['register.']['generatepassword.']['length']) {
				$password['normal'] .= $chars{mt_rand(0, strlen($chars))};
				$i++;
			}

		}

		// Wenn "saltedpasswords" installiert ist wird deren Konfiguration geholt, und je nach Einstellung das Password verschluesselt.
		if (t3lib_extMgm::isLoaded('saltedpasswords')) {
			$saltedpasswords = tx_saltedpasswords_div::returnExtConf();

			if ($saltedpasswords['enabled'] == 1) {
				$tx_saltedpasswords = t3lib_div::makeInstance($saltedpasswords['saltedPWHashingMethod']);
				$password['encrypted'] = $tx_saltedpasswords->getHashedPassword($password['normal']);
			}
		}

		// Wenn "md5passwords" installiert ist wird wenn aktiviert, das Password md5 verschluesselt.
		if (t3lib_extMgm::isLoaded('md5passwords')) {
			$arrConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['md5passwords']);

			if ($arrConf['activate'] == 1) {
				$password['encrypted'] = md5($password['normal']);
			}
		}

		// Wenn "t3sec_saltedpw" installiert ist wird wenn aktiviert, das Password gehashed.
		if (t3lib_extMgm::isLoaded('t3sec_saltedpw')) {
			require_once t3lib_extMgm::extPath('t3sec_saltedpw') . 'res/staticlib/class.tx_t3secsaltedpw_div.php';

			if (tx_t3secsaltedpw_div::isUsageEnabled()) {
				require_once t3lib_extMgm::extPath('t3sec_saltedpw') . 'res/lib/class.tx_t3secsaltedpw_phpass.php';
				$tx_t3secsaltedpw_phpass = t3lib_div::makeInstance('tx_t3secsaltedpw_phpass');
				$password['encrypted'] = $tx_t3secsaltedpw_phpass->getHashedPassword($password['normal']);
			}
		}

		return $password;
	}

	/**
	 * Erstellt ein neues Passwort, falls Passwort generieren eingestellt ist. Das Passwort kannn dann ueber den Marker "###PASSWORD###" mit der Registrierungsmail gesendet werden.
	 *
	 * @param	string		$userId
	 * @return	array
	 */
	function generatePasswordForMail($userId) {
		$extraMarkers = array();

		if ($this->conf['register.']['generatepassword.']['mode'] && $userId) {
			$password = $this->generatePassword('password');
			$extraMarkers['password'] = $password['normal'];
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $userId , array('password' => $password['encrypted']));
		}

		return $extraMarkers;
	}

	/**
	 * Ueberprueft anhand der aktuellen Verschluesselungsextension, ob das uebergebene unverschluesselte Passwort mit dem uebergebenen verschluesselten Passwort uebereinstimmt.
	 *
	 * @param	string		$submitedPassword
	 * @param	string		$originalPassword
	 * @return	boolean
	 */
	function checkPassword($submitedPassword, $originalPassword) {
		$check = false;

		// Wenn "saltedpasswords" installiert ist wird deren Konfiguration geholt, und je nach Einstellung das Password ueberprueft.
		if (t3lib_extMgm::isLoaded('saltedpasswords')) {
			$saltedpasswords = tx_saltedpasswords_div::returnExtConf();

			if ($saltedpasswords['enabled'] == 1) {
				$tx_saltedpasswords = t3lib_div::makeInstance($saltedpasswords['saltedPWHashingMethod']);
				if ($tx_saltedpasswords->checkPassword($submitedPassword, $originalPassword)) {
					$check = true;
				}
			}
		}

		// Wenn "md5passwords" installiert ist wird wenn aktiviert, das Password ueberprueft.
		if (t3lib_extMgm::isLoaded('md5passwords')) {
			$arrConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['md5passwords']);

			if ($arrConf['activate'] == 1) {
				if (md5($submitedPassword) == $originalPassword) {
					$check = true;
				}
			}
		}

		// Wenn "t3sec_saltedpw" installiert ist wird wenn aktiviert, das Password ueberprueft.
		if (t3lib_extMgm::isLoaded('t3sec_saltedpw')) {
			require_once t3lib_extMgm::extPath('t3sec_saltedpw') . 'res/staticlib/class.tx_t3secsaltedpw_div.php';

			if (tx_t3secsaltedpw_div::isUsageEnabled()) {
				require_once t3lib_extMgm::extPath('t3sec_saltedpw') . 'res/lib/class.tx_t3secsaltedpw_phpass.php';
				$tx_t3secsaltedpw_phpass = t3lib_div::makeInstance('tx_t3secsaltedpw_phpass');
				if ($tx_t3secsaltedpw_phpass->checkPassword($submitedPassword, $originalPassword)) {
					$check = true;
				}
			}
		}

		return $check;
	}

	/**
	 * The saveDeleteImage method is used to update or delete an image of an address
	 *
	 * @param	string		$fieldName
	 * @param	array		$arrUpdate // Call by reference Array mit allen zu updatenden Daten.
	 * @return	string
	 */
	function saveDeleteImage($fieldName, &$arrUpdate) {
		// Verzeichniss ermitteln.
		$uploadFolder = $this->feUsersTca['columns'][$fieldName]['config']['uploadfolder'];

		if (substr($uploadFolder, -1) != '/') {
			$uploadFolder = $uploadFolder . '/';
		}

		// Bild loeschen und ueberpruefen ob das Bild auch wirklich existiert.
		if ($this->piVars[$this->contentId][$fieldName . '_delete']) {
			$arrUpdate[$fieldName] = '';

			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fieldName, 'fe_users', 'uid = ' . $this->userId , '', '', '1');
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

			$imagePath = t3lib_div::getFileAbsFileName($uploadFolder . $row[$fieldName]);

			if ($imagePath && file_exists($imagePath)) {
				unlink($imagePath);
			}

			return '';
		}

		// Konfigurierte Dateigroesse ermitteln.
		$maxSize = $this->feUsersTca['columns'][$fieldName]['config']['max_size'] * 1024;

		// Konfigurierte maximale Dateigroesse ueberschritten.
		if ($maxSize && $_FILES[$this->prefixId]['size'][$this->contentId][$fieldName] > $maxSize) {
			return 'size';
		}

		// Der Upload war nicht vollstaendig, da Datei zu gross (Zeitueberschreitung).
		if ($_FILES[$this->prefixId]['error'][$this->contentId][$fieldName] == '2') {
			return 'size';
		}

		// Die erlaubten MIME-Typen.
		$mimeTypes = array();
		$mimeTypes['image/jpeg'] = '.jpg';
		$mimeTypes['image/gif'] = '.gif';
		$mimeTypes['image/bmp'] = '.bmp';
		$mimeTypes['image/tiff'] = '.tif';
		$mimeTypes['image/png'] = '.png';

		// Den Format-Typ ermitteln.
		$imageType = $mimeTypes[$_FILES[$this->prefixId]['type'][$this->contentId][$fieldName]];

		// Wenn ein falsche Format hochgeladen wurde.
		if (!$imageType) {
			return 'type';
		}

		// Nur wenn eine Datei ausgewaehlt wurde [image] und diese den obigen mime-typen enstpricht[$type], dann wird die datei gespeichert
		if ($_FILES[$this->prefixId]['name'][$this->contentId][$fieldName]) {
			// Bildname generieren.
			$fileName = preg_replace("/[^a-zA-Z0-9]/", '', $this->piVars[$this->contentId]['username']) . '_' . time() . $imageType;

			// Kompletter Bildpfad.
			$uploadFile = $uploadFolder . $fileName;

			// Bild verschieben, und anschliessend den neuen Bildnamen in die Datenbank schreiben.
			if (move_uploaded_file($_FILES[$this->prefixId]['tmp_name'][$this->contentId][$fieldName], $uploadFile)) {
				chmod($uploadFile, 0644);
				$arrUpdate[$fieldName] = $fileName;

				// Wenn Das Bild erfolgreich hochgeladen wurde, nichts zurueckgeben.
				return '';
			}
		}

		return 'upload';
	}

	/**
	 * Erledigt allen Output der nichts mit dem eigendlichen Formular zu tun hat.
	 * Fuer besondere Faelle kann hier eine Ausnahme, oder zusaetzliche Konfigurationen gesetzt werden.
	 *
	 * @param	string		$mode
	 * @param	string		$submode
	 * @param	array		$params
	 * @return	string
	 */
	function showMessageOutputRedirect($mode, $submode = '', $params = array()) {
		$redirect = true;

		// Label ermitteln
		$label = $this->getLabel($mode . (($submode) ? '_' . $submode : ''));

		// Zusaetzliche Konfigurationen die gesetzt werden, bevor die Ausgabe oder der Redirect ausgefuehrt werden.
		switch ($mode) {

			case 'register':
				// Login vollziehen, falls eine Redirectseite angegeben ist, wird dorthin automatisch umgeleitet.
				if ($params['username'] && $this->conf['register.']['autologin']) {
					$this->userAutoLogin($params['username'], $mode);
				}

				break;

			case 'doubleoptin':
				// Login vollziehen, falls eine Redirectseite angegeben ist, wird dorthin automatisch umgeleitet.
				if ($params['userId'] && $this->conf['register.']['autologin']) {
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('username', 'fe_users', 'uid = ' . $params['userId'], '', '', '1');
					$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

					$this->userAutoLogin($row['username'], $mode);
				}

			case 'adminapproval':
				// WICHTIG: Kein break beim doubleoptin, da diese Konfiguration auch fuer doubleoptin gilt.
				if ($params['mode']) {
					if ($this->conf['redirect.'][$params['mode']]) {
						$mode = $params['mode'];
					} else {
						$redirect = false;
					}
				}

				break;

			case 'edit_error_no_login':
				$label = '<div class="edit_error_no_login">' . $label . '</div>';
				break;

			case 'edit':
				// Einen Refresh der aktuellen Seite am Client ausfuehren, damit nach dem Editieren wieder das Formular angezeigt wird.
				$GLOBALS['TSFE']->additionalHeaderData['refresh'] = '<meta http-equiv="refresh" content="2; url=' . t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->pi_getPageLink($GLOBALS['TSFE']->id) . '" />';
				break;

			case 'doubleoptin':
				// Einen Refresh auf der aktuellen Seite am Client ausfuehren, damit nach dem Loeschen des Users die Startseite angezeigt wird.
				$GLOBALS['TSFE']->additionalHeaderData['refresh'] = '<meta http-equiv="refresh" content="2; url=' . t3lib_div::getIndpEnv('TYPO3_SITE_URL') . '" />';
				break;

		}

		if ($redirect && $this->conf['redirect.'][$mode]) {
			$this->userRedirect($this->conf['redirect.'][$mode]);
		}

		return $label;
	}

	/**
	 * Vollzieht einen Login ohne ein Passwort.
	 *
	 * @param	string		$username
	 * @param	string		$mode
	 * @return	void
	 */
	function userAutoLogin($username, $mode = '') {
		// Login vollziehen.
		$GLOBALS['TSFE']->fe_user->checkPid = 0;
		$info = $GLOBALS['TSFE']->fe_user->getAuthInfoArray();
		$user = $GLOBALS['TSFE']->fe_user->fetchUserRecord($info['db_user'], $username);
		$GLOBALS['TSFE']->fe_user->createUserSession($user);

		// Umleiten, damit der Login wirksam wird.
		$this->userRedirect($this->conf['redirect.'][$mode]);
	}

	/**
	 * Vollzieht einen Redirect mit der Seite die benutzt wird, oder auf die aktuelle.
	 *
	 * @param	int			$pageId
	 * @return	void
	 */
	function userRedirect($pageId = 0) {
		// Normalen Redirect, oder Redirect auf die gewuenschte Seite.
		if (!$pageId) {
			$pageId = $GLOBALS['TSFE']->id;
		}

		$pageLink = $this->pi_getPageLink($pageId);
		header('Location: ' . $pageLink . ((strpos($pageLink, '?') === false) ? '?' : '&') . $this->makeHiddenParams());
		exit;
	}

	/**
	 * Sendet die Aktivierungsmail an den uebergebenen User.
	 *
	 * @param	int			$userId
	 * @return	void
	 */
	function sendActivationMail($userId) {
		// Neuen Timestamp setzten, damit jede Aktivierungsmail einen anderen Hash hat.
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . intval($userId),array('tstamp' => time()));

		// Userdaten ermitteln.
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, tstamp, username, email, tx_datamintsfeuser_approval_level', 'fe_users', 'uid = ' . intval($userId), '', '', '1');
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		// Genehmigungstypen aufsteigend sortiert ermitteln. Das ist nötig um das Level dem richtigen Typ zuordnen zu können.
		// Beispiel: approvalcheck = ,doubleoptin,adminapproval => beim exploden kommt dann ein leeres Arrayelement herraus, das nach dem entfernen einen leeren Platz uebrig lässt.
		$arrApprovalTypes = $this->getApprovalTypes();

		// Aktuellen Genehmigungstyp ermitteln.
		$approvalType = $arrApprovalTypes[count($arrApprovalTypes) - $row['tx_datamintsfeuser_approval_level']];

		// Mail vorbereiten.
		$hash = md5($row['uid'] . $row['tstamp'] . $row['username'] . $row['email']);
		$pageLink = $this->pi_getPageLink($GLOBALS['TSFE']->id);
		$pageLink = (strpos($pageLink, '?') === false) ? $pageLink . '?' : $pageLink . '&';
		$extraMarkers = array(
			'approvallink' => t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $pageLink . $this->prefixId . '%5B' . $this->contentId . '%5D%5Bsubmit%5D=approvalcheck&' . $this->prefixId . '%5B' . $this->contentId . '%5D%5Buid%5D=' . $row['uid'] . '&' . $this->prefixId . '%5B' . $this->contentId . '%5D%5Bhash%5D=' . $hash . $this->makeHiddenParams()
		);

		// E-Mail senden.
		$this->sendMail($row['uid'], $approvalType, $this->isAdminMail($approvalType), $extraMarkers);

		// Cookie fuer das erneute zusenden des Aktivierungslinks setzten.
		$this->setNotActivatedCookie($row['uid']);
	}

	/**
	 * Ueberprueft ob die Linkestaetigung gueltig ist und aktiviert gegebenenfalls den User.
	 *
	 * @param	int			$userId
	 * @return	string
	 */
	function makeApprovalCheck($userId) {
		// Userdaten ermitteln.
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, tstamp, username, email, tx_datamintsfeuser_approval_level', 'fe_users', 'uid = ' . $userId, '', '', '1');
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		// Genehmigungstyp ermitteln um die richtige E-Mail zu senden, bzw. die richtige Ausgabe zu ermitteln.
		$arrApprovalTypes = $this->getApprovalTypes();
		$approvalType = $arrApprovalTypes[count($arrApprovalTypes) - $row['tx_datamintsfeuser_approval_level']];

		// Wenn kein Genehmigungstyp ermittelt werden konnte.
		if (!$approvalType) {
			return $this->showMessageOutputRedirect('approvalcheck_failure');
		} else {
			// Ausgabe vorbereiten.
			$mode = $approvalType;
			$submode = 'failure';
			$params = array();
		}

		// Daten vorbereiten.
		$time = time();
		$hash = md5($row['uid'] . $row['tstamp'] . $row['username'] . $row['email']);

		// Wenn der Hash richtig ist, des letzte Genehmigungslevel aber noch nicht erreicht ist.
		if ($this->piVars[$this->contentId]['hash'] == $hash && $row['tx_datamintsfeuser_approval_level'] > 1) {
			// Genehmigungslevel updaten.
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $userId ,array('tstamp' => $time, 'tx_datamintsfeuser_approval_level' => $row['tx_datamintsfeuser_approval_level'] - 1));

			// Aktivierungsmail schicken.
			$this->sendActivationMail($userId);

			// Ausgabe vorbereiten.
			$submode = 'success';
		}

		// Wenn der Hash richtig ist, und das letzte Genehmigungslevel erreicht ist.
		if ($this->piVars[$this->contentId]['hash'] == $hash && $row['tx_datamintsfeuser_approval_level'] == 1) {
			// Erstellt ein neues Passwort, falls Passwort generieren eingestellt ist. Das Passwort kannn dann ueber den Marker "###PASSWORD###" mit der Registrierungsmail gesendet werden.
			$extraMarkers = $this->generatePasswordForMail(intval($this->piVars[$this->contentId]['uid']));

			// User aktivieren.
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $userId ,array('tstamp' => $time, 'disable' => '0', 'tx_datamintsfeuser_approval_level' => '0'));

			// Registrierungsemail schicken.
			$this->sendMail($userId, 'registration', true);
			$this->sendMail($userId, 'registration', false, $extraMarkers);

			// Ausgabe vorbereiten.
			$submode = 'success';
			$params = array('userId' => $userId);
		}

		return $this->showMessageOutputRedirect($mode, $submode, $params);
	}

	/**
	 * Ermittelt alle Genehmigungstypen.
	 *
	 * @return	array
	 */
	function getApprovalTypes() {
		// Genhemigungstypen aufsteigend sortiert ermitteln. Das ist nötig um das Level dem richtigen Typ zuordnen zu können.
		// Beispiel: approvalcheck = ,doubleoptin,adminapproval => beim exploden kommt dann ein leeres Arrayelement herraus, das nach dem entfernen einen leeren Platz uebrig lässt.
		return array_values($this->cleanArray(t3lib_div::trimExplode(',', $this->conf['register.']['approvalcheck'])));
	}

	/**
	 * Ueberprueft anhand des Genehmigungstyps ob die Mail eine Adminmail oder eine Usermail ist. Wenn 'admin' im Namen des Genehmigungstyps steht, dann ist die Mail eine Adminmail.
	 *
	 * @param	string		$approvalType
	 * @return	boolean
	 */
	function isAdminMail($approvalType) {
		return (strpos($approvalType, 'admin') === false) ? false : true;
	}

	/**
	 * Setzt einen Cookie fuer den neu angelegten Account, falls dieser aktiviert werden muss.
	 *
	 * @param	int			$userId
	 * @return	void
	 */
	function setNotActivatedCookie($userId) {
		$arrNotActivated = $this->getNotActivatedUserArray();
		$arrNotActivated[] = intval($userId);
		setcookie($this->prefixId . '[not_activated]', implode(',', $arrNotActivated), time() + 60 * 60 * 24 * 30);
	}

	/**
	 * Ermittelt alle nicht aktivierten Accounts des Users, falls .
	 *
	 * @param	array		$arrNotActivated
	 * @return	array
	 */
	function getNotActivatedUserArray($arrNotActivated = array()) {
		$arrNotActivatedCleaned = array();

		// Nicht aktivierte User ueber den Cookie ermitteln, und vor missbrauch schuetzen.
		if (!$arrNotActivated) {
			$arrNotActivated = $this->cleanArray(array_unique(t3lib_div::trimExplode(',', $_COOKIE[$this->prefixId]['not_activated'])));
		}

		foreach ($arrNotActivated as $key => $val) {
			$arrNotActivated[$key] = intval($val);
		}

		// Wenn nach dem reinigen noch User uebrig bleiben.
		if (count($arrNotActivated) > 0) {
			// Herrausgefundene User ermitteln und ueberpruefen, ob die User mitlerweile schon aktiviert wurden.
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'fe_users', 'uid IN(' . implode(',', $arrNotActivated) . ') AND disable = 1 AND deleted = 0');

			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$arrNotActivatedCleaned[] = $row['uid'];
			}
		}

		return $arrNotActivatedCleaned;
	}

	/**
	 * Sendet die E-Mails mit dem uebergebenen Template und falls angegeben, auch mit den extra Markern.
	 *
	 * @param	int			$userId
	 * @param	string		$templatePart
	 * @param	boolean		$adminMail
	 * @param	array		$extraMarkers
	 * @return	void
	 */
	function sendMail($userId, $templatePart, $adminMail = true, $extraMarkers = array()) {
		// Userdaten ermitteln.
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', 'uid = ' . intval($userId), '', '', '1');
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		$markerArray = array_merge((array)$row, (array)$extraMarkers);

		foreach ($markerArray as $key => $val) {
			if (!$this->checkUtf8($val)) {
				$markerArray[$key] = utf8_encode($val);
			}
		}

		// Wenn die Mail fuer den Admin bestimmt ist.
		if ($adminMail) {
			// Template laden.
			$template = $this->getTemplateSubpart($templatePart . '_admin', $markerArray);

			// E-Mail und Name ermitteln.
			$email = $this->conf['register.']['adminmail'];
			$name = ($this->conf['register.']['adminname']) ? $this->conf['register.']['adminname'] : $email;
		} else {
			// Template laden.
			$template = $this->getTemplateSubpart($templatePart, $markerArray);

			// E-Mail und Name ermitteln.
			$email = $row['email'];
			$name = ($row['name']) ? $row['name'] : $email;
		}

		// Betreff ermitteln und aus dem E-Mail Content entfernen.
		$subject = trim($this->cObj->getSubpart($template, '###SUBJECT###'));
		$template = $this->cObj->substituteSubpart($template, '###SUBJECT###', '');

		// Restlichen Content wieder zusammenfuegen.
		if ($this->conf['register.']['mailtype'] == 'html') {
			$mailtype = 'text/html';
		} else {
			$mailtype = 'text/plain';
			$template = trim(strip_tags($template));
		}

		// Zusaetzliche Header User-Mail.
		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: ' . $mailtype . '; charset=utf-8' . "\r\n";
		$header .= 'From: ' . $this->conf['register.']['sendername'] . ' <' . $this->conf['register.']['sendermail'] . '>' . "\r\n";
		$header .= 'X-Mailer: PHP/' . phpversion();

		// Verschicke E-Mail.
		if ($email) {
			mail($name . ' <' . $email . '>', $subject, $template, $header);
		}
	}

	/**
	 * Holt einen Subpart des Standardtemplates und ersetzt uebergeben Marker.
	 *
	 * @param	string		$templatePart
	 * @param	array		$markerArray
	 * @return	string
	 */
	function getTemplateSubpart($templatePart, $markerArray = array()) {
		// Template holen.
		$templateFile = $this->conf['register.']['emailtemplate'];

		if (!$templateFile) {
			$templateFile = 'EXT:' . $this->extKey . '/res/datamints_feuser_mail.html';
		}

		// Template laden.
		$template = $this->cObj->fileResource($templateFile);
		$template = $this->cObj->getSubpart($template, '###' . strtoupper($templatePart) . '###');

		if (!$this->checkUtf8($template)) {
			$template = utf8_encode($template);
		}

		$template = $this->cObj->substituteMarkerArray($template, $markerArray, '###|###', 1);

		return $template;
	}

	/**
	 * Gibt alle im Backend definierten Felder (TypoScipt/Flexform) formatiert und der Anzeigeart entsprechend aus.
	 *
	 * @param	array		$valueCheck
	 * @return	string		$content
	 */
	function showForm($valueCheck = array()) {
		$arrCurrentData = array();

		// Beim editieren der Userdaten, die Felder vorausfuellen.
		if ($this->conf['showtype'] == 'edit' && $this->userId) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', 'uid = ' . $this->userId , '', '', '1');
            $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

			$arrCurrentData = $row;
		}

		// Wenn das Formular schon einmal abgesendet wurde aber ein Fehler auftrat, dann die bereits vom User uebertragenen Userdaten vorausfuellen.
		if ($this->piVars[$this->contentId]) {
			foreach ($this->piVars[$this->contentId] as $key => $val) {
				$this->piVars[$this->contentId][$key] = strip_tags($val);
			}

			$arrCurrentData = array_merge((array)$row, (array)$this->piVars[$this->contentId]);
		}

		// Konvertiert alle moeglichen Zeichen der Ausgabe, die stoeren koennten (XSS).
		if ($arrCurrentData) {
			foreach ($arrCurrentData as $key => $val) {
				$arrCurrentData[$key] = htmlspecialchars($val);
			}
		}

		// Seite, die den Request entgegennimmt (TypoLink).
		$requestLink = $this->pi_getPageLink($this->conf['requestpid']);

		// Wenn keine Seite per TypoScript angegeben ist, wird die aktuelle Seite verwendet.
		if (!$this->conf['requestpid']) {
			$requestLink = $this->pi_getPageLink($GLOBALS['TSFE']->id);
		}

		// ID Zaehler fuer Items und Fieldsets.
		$iItem = 1;
		$iFieldset = 1;
		$iInfoItem = 1;

		// Formular start.
		$content = '<form id="' . $this->extKey . '_' . $this->contentId . '_form" name="' . $this->prefixId . '[' . $this->contentId . ']" action="' . $requestLink . '" method="post" enctype="multipart/form-data"><fieldset class="form_fieldset_' . $iFieldset . '">';

		// Wenn eine Lgende fuer das erste Fieldset definiert wurde, diese ausgeben.
		if ($this->conf['legends.'][$iFieldset]) {
			$content .= '<legend class="form_legend_' . $iFieldset . '">' . $this->conf['legends.'][$iFieldset] . '</legend>';
		}

		// Alle ausgewaehlten Felder durchgehen.
		foreach ($this->arrUsedFields as $fieldName) {
			// Wenn das im Flexform ausgewaehlte Feld existiert, dann dieses Feld ausgeben, alle anderen Felder werden ignoriert.
			if ($this->feUsersTca['columns'][$fieldName]) {
				// Form Item Anfang.
				$content .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_wrapper" class="form_item form_item_' . $iItem . ' form_type_' . $this->feUsersTca['columns'][$fieldName]['config']['type'] . '">';

				// Label schreiben.
				$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '">' . $this->getLabel($fieldName) . '</label>';

				switch ($this->feUsersTca['columns'][$fieldName]['config']['type']) {

					case 'input':
						$content .= $this->showInput($fieldName, $arrCurrentData, $iItem);
						break;

					case 'text':
						$content .= $this->showText($fieldName, $arrCurrentData);
						break;

					case 'check':
						$content .= $this->showCheck($fieldName, $arrCurrentData);
						break;
/*
					case 'radio':
						for ($j = 0; $j < count($this->feUsersTca['columns'][$fieldName]['config']['items']); $j++) {
							$checked = ($arrCurrentData[$fieldName] == $this->feUsersTca['columns'][$fieldName]['config']['items'][$j][1]) ? ' checked="checked"' : '';
							$content .= '<input type="radio" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_' . $j . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="' . $this->feUsersTca['columns'][$fieldName]['config']['items'][$j][1] . '"' . $checked . ' class="radiobutton" />';
							$content .= '<label class="radio_label" for="' . $this->prefixId . '_' . $fieldName . '_' . $j . '">';
							$content .= $this->getLabel($this->feUsersTca['columns'][$fieldName]['config']['items'][$j][0]);
							$content .= '</label>';
						}
						break;
*/
					case 'select':
						$content .= $this->showSelect($fieldName, $arrCurrentData);
						break;

					case 'group':
						$content .= $this->showGroup($fieldName, $arrCurrentData);
						break;

				}

				// Extra Error Label ermitteln.
				$content .= $this->getErrorLabel($fieldName, $valueCheck);

				// Form Item Ende.
				$content .= '</div>';

				$iItem++;
			}

			// Separator anzeigen.
			if ($fieldName == '--separator--') {
				$iFieldset++;

				$content .= '</fieldset><fieldset class="form_fieldset_' . $iFieldset . '">';

				// Wenn eine Lgende fuer das Fieldset definiert wurde, diese ausgeben.
				if ($this->conf['legends.'][$iFieldset]) {
					$content .= '<legend class="form_legend_' . $iFieldset . '">' . $this->conf['legends.'][$iFieldset] . '</legend>';
				}
			}

			// Infoitem anzeigen.
			if ($fieldName == '--infoitem--') {
				if ($this->conf['infoitems.'][$iInfoItem]) {
					$content .= '<div class="form_infoitem_' . $iInfoItem . '">' . $this->conf['infoitems.'][$iInfoItem] . '</div>';
				}

				$iInfoItem++;
			}

			// Profil loeschen Link anzeigen.
			if ($fieldName == '--userdelete--' && $this->conf['showtype'] == 'edit' && $this->userId) {
				$fieldName = $this->cleanSpecialFieldKey($fieldName);

				$content .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_wrapper" class="form_item form_item_' . $iItem . ' form_type_' . $fieldName . '">';
				$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '">' . $this->getLabel($fieldName) . '</label>';
				$content .= '<input type="checkbox" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="1" />';
				$content .= $this->getErrorLabel($fieldName, $valueCheck);
				$content .= '</div>';

				$iItem++;
			}

			// Passwortbestaetigung anzeigen.
			if ($fieldName == '--passwordconfirmation--' && $this->conf['showtype'] == 'edit' && $this->userId) {
				$fieldName = $this->cleanSpecialFieldKey($fieldName);

				$content .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_wrapper" class="form_item form_item_' . $iItem . ' form_type_' . $fieldName . '">';
				$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '">' . $this->getLabel($fieldName) . '</label>';
				$content .= '<input type="password" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="" />';
				$content .= $this->getErrorLabel($fieldName, $valueCheck);
				$content .= '</div>';

				$iItem++;
			}

			// Aktivierung erneut senden anzeigen.
			if ($fieldName == '--resendactivation--') {
				$fieldName = $this->cleanSpecialFieldKey($fieldName);

				// Noch nicht fertig gestellte Listenansicht der nichta aktivierten User.
				//if ($this->conf['shownotactivated'] == 'list') {
				//	$arrNotActivated = $this->getNotActivatedUserArray();
				//	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, username', 'fe_users', 'pid = ' . intval($this->storagePid) . ' AND uid IN(' . implode(',', $arrNotActivated) . ') AND disable = 1 AND deleted = 0');

				//	while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				//		$content .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_wrapper" class="form_item form_item_' . $iItem . ' form_type_' . $fieldName . ' ' . $this->conf['shownotactivated'] . '">';
				//		$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '">' . $this->getLabel($fieldName) . ' ' . $row['username'] . '</label>';
				//		$content .= '<input type="checkbox" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . '][' . $row['uid'] . ']" value="1" />';
				//		$content .= '</div>';

				//		$iItem++;
				//	}
				//} else {
					$content .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_wrapper" class="form_item form_item_' . $iItem . ' form_type_' . $fieldName . '">';
					$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '">' . $this->getLabel($fieldName) . '</label>';
					$content .= '<input type="text" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="" />';
					$content .= $this->getErrorLabel($fieldName, $valueCheck);
					$content .= '</div>';

					$iItem++;
				//}
			}

			// Submit Button anzeigen.
			if ($fieldName == '--submit--') {
				$fieldName = $this->cleanSpecialFieldKey($fieldName);

				$content .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_wrapper" class="form_item form_item_' . $iItem . ' form_type_' . $fieldName . '">';
				$content .= '<input id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" type="submit" value="' . $this->getLabel($fieldName . '_' . $this->conf['showtype']) . '"/>';
				$content .= '</div>';

				$iItem++;
			}
		}

		// UserId, PageId und Modus anhaengen.
		$content .= '<input type="hidden" name="' . $this->prefixId . '[' . $this->contentId . '][submit]" value="send" />';
		$content .= '<input type="hidden" name="' . $this->prefixId . '[' . $this->contentId . '][userid]" value="' . $this->userId . '" />';
		$content .= '<input type="hidden" name="' . $this->prefixId . '[' . $this->contentId . '][pageid]" value="' . $GLOBALS['TSFE']->id . '" />';
		$content .= '<input type="hidden" name="' . $this->prefixId . '[' . $this->contentId . '][submitmode]" value="' . $this->conf['showtype'] . '" />';
		$content .= $this->makeHiddenFields();

		$content .= '</fieldset>';
		$content .= '</form>';

		return $content;
	}

	/**
	 * Ersetzt die beim Eingeben angegebenen '--' Zeichen vor und hinter dem eigendlichen Feldnamen, falls vorhanden.
	 *
	 * @param	string		$fieldName
	 * @return	string
	 */
	function cleanSpecialFieldKey($fieldName) {
		if (preg_match('/^--.*--$/', $fieldName)) {
			return preg_replace('/^--(.*)--$/', '\1', $fieldName);
		}
		return $fieldName;
	}

	/**
	 * Rendert Inputfelder.
	 *
	 * @param	string		$fieldName
	 * @param	array		$arrCurrentData
	 * @param	int			$iItem
	 * @return	string		$content
	 */
	function showInput($fieldName, $arrCurrentData, $iItem) {
		$content = '';

		// Datumsfeld.
		if (strpos($this->feUsersTca['columns'][$fieldName]['config']['eval'], 'date') !== false) {
			// Timestamp zu "tt.mm.jjjj" machen.
			if ($arrCurrentData[$fieldName] != 0) {
				$datum = strftime('%d.%m.%Y', $arrCurrentData[$fieldName]);
			}

			$content .= '<input type="text" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="' . $datum . '" />';

			return $content;
		}

		// Passwordfelder.
		if (strpos($this->feUsersTca['columns'][$fieldName]['config']['eval'], 'password') !== false) {
			$content .= '<input type="password" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="" />';
			$content .= '</div><div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_rep_wrapper" class="form_item form_item_' . $iItem . ' form_type_' . $this->feUsersTca['columns'][$fieldName]['config']['type'] . '">';
			$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_rep">' . $this->getLabel($fieldName . '_rep') . $this->checkIfRequired($fieldName) . '</label>';
			$content .= '<input type="password" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_rep" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . '_rep]" value="" />';

			return $content;
		}

		// Normales Inputfeld.
		$readOnly = ($this->feUsersTca['columns'][$fieldName]['config']['readOnly'] == 1) ? ' readonly="readonly"' : '';
		$content .= '<input type="text" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="' . $arrCurrentData[$fieldName] . '"' . $readOnly . ' />';

		return $content;
	}

	/**
	 * Rendert Textareas.
	 *
	 * @param	string		$fieldName
	 * @param	array		$arrCurrentData
	 * @return	string		$content
	 */
	function showText($fieldName, $arrCurrentData) {
		$content = '';

		$readOnly = ($this->feUsersTca['columns'][$fieldName]['config']['readOnly'] == 1) ? ' readonly="readonly"' : '';
		$content .= '<textarea id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" rows="2" cols="42"' . $readOnly . '>' . $arrCurrentData[$fieldName] . '</textarea>';

		return $content;
	}

	/**
	 * Rendert Checkboxen.
	 *
	 * @param	string		$fieldName
	 * @param	array		$arrCurrentData
	 * @return	string		$content
	 */
	function showCheck($fieldName, $arrCurrentData) {
		$content = '';

		$checked = ($arrCurrentData[$fieldName] == 1) ? ' checked="checked"' : '';
		$content .= '<input type="checkbox" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="1"' . $checked . ' />';

		return $content;
	}

	/**
	 * Rendert Selectfelder.
	 *
	 * @param	string		$fieldName
	 * @param	array		$arrCurrentData
	 * @return	string		$content
	 */
	function showSelect($fieldName, $arrCurrentData) {
		$content = '';
		$optionlist = '';
		$countSelectFields = count($this->feUsersTca['columns'][$fieldName]['config']['items']);

		// Items, die in der TCA-Konfiguration festgelegt wurden.
		for ($i = 0; $i < $countSelectFields; $i++) {
			$selected = (strpos($arrCurrentData[$fieldName], $i) !== false || in_array($i, $arrCurrentData[$fieldName])) ? ' selected="selected"' : '';
			$optionlist .= '<option value="' . $this->feUsersTca['columns'][$fieldName]['config']['items'][$i][1] . '"' . $selected . '>' . $this->getLabel($this->feUsersTca['columns'][$fieldName]['config']['items'][$i][0]) . '</option>';
		}

		// Wenn Tabelle angegeben zusaetzlich Items aus Datenbank holen.
		if ($this->feUsersTca['columns'][$fieldName]['config']['foreign_table']) {
			// Select-Items aus DB holen.
			$tab = $this->feUsersTca['columns'][$fieldName]['config']['foreign_table'];
			$sel = 'uid, ' . $GLOBALS['TCA'][$tab]['ctrl']['label'];
			$whr = $this->feUsersTca['columns'][$fieldName]['config']['foreign_table_where'];

			// Wenn OrderBy ganz vorne in $whr steht, dann muss eine 1 davor plaziert werden, da sonst die Abfrage ungueltig ist.
			$whr = (strtolower(substr(trim($whr), 0, 8)) == 'order by') ? '1 ' . $whr : $whr;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($sel , $tab, $whr);

			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$selected = (strpos($arrCurrentData[$fieldName], $row['uid']) !== false || in_array($row['uid'], $arrCurrentData[$fieldName])) ? ' selected="selected"' : '';
				$optionlist .= '<option value="' . $row['uid'] . '"' . $selected . '>' . $row[$GLOBALS['TCA'][$tab]['ctrl']['label']] . '</option>';
			}
		}

		if ($this->feUsersTca['columns'][$fieldName]['config']['size'] == 1) {
			// Einzeiliges Select (Dropdown).
			$content .= '<select id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']">';
			$content .= $optionlist;
			$content .= '</select>';
		} else {
			// Mehrzeiliges Select (Auswahlliste).
			$content .= '<select id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . '][]" size="' . $this->feUsersTca['columns'][$fieldName]['config']['size'] . '" multiple="multiple">';
			$content .= $optionlist;
			$content .= '</select>';
		}

		return $content;
	}

	/**
	 * Rendert Groupfelder.
	 *
	 * @param	string		$fieldName
	 * @param	array		$arrCurrentData
	 * @return	string		$content
	 */
	function showGroup($fieldName, $arrCurrentData) {
		$content = '';

		// GROUP (z.B. externe Tabellen).
		// Wenn es sich um den "internal_type" FILE handelt && es ein Bild ist, dann ein Vorschaubild erstellen und ein Fiel-Inputfeld anzeigen.
		if ($this->feUsersTca['columns'][$fieldName]['config']['internal_type'] == 'file') {
			// Verzeichniss ermitteln.
			$uploadFolder = $this->feUsersTca['columns'][$fieldName]['config']['uploadfolder'];

			if (substr($uploadFolder, -1) != '/') {
				$uploadFolder = $uploadFolder . '/';
			}

			// Breite ermitteln.
			$imageWidth = $this->conf['image.']['maxwidth'];

			if (!$imageWidth) {
				$imageWidth = 100;
			}

			$imgTSConfig = $this->conf['image.'];
			$imgTSConfig['file'] = $uploadFolder . $arrCurrentData[$fieldName];
			$imgTSConfig['file.']['maxW'] = $imageWidth;
			$imgTSConfig['altText'] = 'Bild';
			$imgTSConfig['titleText'] = 'Bild';
			$image = $this->cObj->IMAGE($imgTSConfig);

			// Bild anzeigen.
			if ($image) {
				$content .= '<div class="image_preview">' . $image . '</div>';
			}

			// Wenn kein Bild vorhanden ist, das Upload-Feld anzeigen.
			if (!$arrCurrentData[$fieldName]) {
				$content .= '<input type="file" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" />';
			} else {
				$content .= '<div class="image_delete"><input type="checkbox" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . '_delete]" />' . $this->getLabel('image_delete') . '</div>';
			}
		}

		return $content;
	}

	/**
	 * Erstellt Hidden Fields fuer vordefinierte Parameter die uebergeben wurden.
	 *
	 * @return	string		$content
	 */
	function makeHiddenFields() {
		$content = '';

		foreach ($this->arrHiddenParams as $paramName) {
			if ($_REQUEST[$paramName]) {
				$content .= '<input type="hidden" name="' . $paramName . '" value="' . htmlspecialchars($_REQUEST[$paramName]) . '" />';
			}
		}

		return $content;
	}

	/**
	 * Erstellt GET-Parameter fuer vordefinierte Parameter die uebergeben wurden.
	 *
	 * @return	string		$params
	 */
	function makeHiddenParams() {
		$params = '';

		foreach ($this->arrHiddenParams as $paramName) {
			if ($_REQUEST[$paramName]) {
				$params .= '&' . urlencode($paramName) . '=' . $this->cleanHeaderUrlData($_REQUEST[$paramName]);
			}
		}

		return $params;
	}

	/**
	 * Konvertiert einen String um ihn in der PHP Funktion header nutzen zu koennen.
	 *
	 * @param	string		$data
	 * @return	string		$data
	 */
	function cleanHeaderUrlData($data) {
		$data = urlencode(strip_tags(preg_replace("/[\r\n]/", '', $data)));

		return $data;
	}

	/**
	 * Ueberprueft ob das uebergebene Feld benoetigt wird um erfolgreich zu speichern.
	 *
	 * @param	string		$fieldName
	 * @return	string
	 */
	function checkIfRequired($fieldName) {
		if (in_array($fieldName, $this->arrRequiredFields)) {
			return ' *';
		} else {
			return '';
		}
	}

	/**
	 * Ermittelt ein bestimmtes Label aufgrund des im TCA gespeicherten Languagestrings, des Datenbankfeldnamens oder gibt einfach den uebergeben Wert wieder aus, wenn nichts gefunden wurde.
	 *
	 * @param	string		$fieldName
	 * @return	string		$label
	 */
	function getLabel($fieldName) {
		if (strpos($fieldName, 'LLL:') === false) {
			// Label aus der Konfiguration holen basierend auf dem Datenbankfeldnamen.
			$label = $this->pi_getLL($fieldName);

			// Das Label zurueckliefern, falls vorhanden.
			if ($label) {
				return $label . $this->checkIfRequired($fieldName);
			}

			// LanguageString ermitteln.
			$languageString = $this->feUsersTca['columns'][$fieldName]['label'];
		} else {
			$languageString = $fieldName;
		}

		// Standard Sprache.
		$defaultLanguage = $this->getDefaultLanguage();
		// Languagekey ermitteln z.B. ("LLL:EXT:lang/locallang_general.php:LGL.starttime" => "LGL.starttime").
		$languageKey = substr($languageString, strripos($languageString, ':') + 1);
		// Languagefile ermitteln z.B. ("LLL:EXT:lang/locallang_general.php:LGL.starttime" => "EXT:lang/locallang_general.php").
		$languageFilePath = substr($languageString, 4, strripos($languageString, ':') - 4);
		// LanguageFile laden.
		$languageFile = $GLOBALS['TSFE']->readLLfile($languageFilePath);
		// Das Label zurueckliefern.
		$label = $languageFile[$defaultLanguage][$languageKey];

		// Das Label zurueckliefern, falls vorhanden.
		if ($label) {
			return $label . $this->checkIfRequired($fieldName);
		}

		// Wenn gar nichts gefunden wurde den uebergebenen Wert wieder zurueckliefern.
		return $fieldName . $this->checkIfRequired($fieldName);
	}

	/**
	 * Ermittelt das Error Lebel aus dem feldnamen.
	 *
	 * @param	string		$fieldName
	 * @param	array		$valueCheck
	 * @return	string
	 */
	function getErrorLabel($fieldName, $valueCheck) {
		$label = '';

		// Extra Error Label ermitteln.
		if (array_key_exists($fieldName, $valueCheck) && is_string($valueCheck[$fieldName])) {
			$label = '<div class="form_error ' . $fieldName . '_error">' . $this->getLabel($fieldName . '_error_' . $valueCheck[$fieldName]) . '</div>';
		}

		return $label;
	}

	/**
	 * Ermittelt die Standard Sprache.
	 *
	 * @return	string		$defaultLanguage
	 */
	function getDefaultLanguage() {
		// Standard Sprache.
		$defaultLanguage = $GLOBALS['TSFE']->config['config']['language'];

		if (!$defaultLanguage) {
			$defaultLanguage = 'default';
		}

		return $defaultLanguage;
	}

	/**
	 * Holt Konfigurationen aus der Flexform (Tab-bedingt) und ersetzt diese pro Konfiguration in der TypoScript Konfiguration.
	 *
	 * @return	void
	 * @global	$this->extConf
	 * @global	$this->conf
	 */
	function getConfiguration() {
		$conf = array();

		// Extension Konfiguration ermitteln.
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);

		// Alle Tabs der Flexformkonfiguration durchgehn.
		foreach ($this->cObj->data['pi_flexform']['data'] as $tabKey => $val) {
			$this->readFlexformTab($this->cObj->data['pi_flexform'], $conf, $tabKey);
		}

		// Alle gesammelten Konfigurationen in $this->conf uebertragen.
		foreach ($conf as $key => $val) {
			if (is_array($val) && $this->extConf['useIRRE']) {
				// Wenn IRRE Konfiguration uebergeben wurde und in der Extension Konfiguration gesetzt ist...
				$this->conf[$key] = $val;
			} else {
				// Alle anderen Konfigurationen...
				$this->setFlexformConfiguration($key, $val);
			}
		}

		// Die IRRE Konfiguration abarbeiten.
		if ($this->extConf['useIRRE'] && $this->conf['databasefields']) {
			$this->setIrreConfiguration();
		}

		// Konfigurationen, die an mehreren Stellen benoetigt werden, in globales Array schreiben.
		$this->arrUsedFields = $this->cleanArray(t3lib_div::trimExplode(',', $this->conf['usedfields']));
		$this->arrRequiredFields = $this->cleanArray(array_unique(t3lib_div::trimExplode(',', $this->conf['requiredfields'])));
		$this->arrUniqueFields = $this->cleanArray(array_unique(t3lib_div::trimExplode(',', $this->conf['uniquefields'])));
		$this->arrHiddenParams = $this->cleanArray(array_unique(t3lib_div::trimExplode(',', $this->conf['hiddenparams'])));

		// Konfigurationen die immer gelten setzten (Feldnamen sind fuer konfigurierte Felder und fuer input Felder).
		$this->arrRequiredFields[] = '--passwordconfirmation--';
	}

	/**
	 * Parst das Flexform Konfigurations Array und schreibt alle Werte in $conf.
	 *
	 * @param	array		$flexData
	 * @param	array		$conf // Call by reference Array mit allen zu updatenden Daten.
	 * @param	string		$sType
	 * @return	void
	 */
	function readFlexformTab($flexData, &$conf, $sTab) {
		 if (is_array($flexData)) {
			 if (isset($flexData['data'][$sTab]['lDEF'])) {
				 $flexData = $flexData['data'][$sTab]['lDEF'];
			 }

			 foreach ($flexData as $key => $value) {
				 if (is_array($value['el']) && count($value['el']) > 0) {
					 foreach ($value['el'] as $ekey => $element) {
						 if (isset($element['vDEF'])) {
							 $conf[$ekey] = $element['vDEF'];
						 } else {
							 $this->readFlexformTab($element, $conf[$key][$ekey], $sTab);
						 }
					 }
				 } else {
					 $this->readFlexformTab($value['el'], $conf, $sTab);
				 }

				 if ($value['vDEF']) {
					 $conf[$key] = $value['vDEF'];
				 }
			 }
		 }
	 }

	/**
	 * Ueberschreibt eventuell vorhandene TypoScript Konfigurationen mit den Konfigurationen aus der Flexform.
	 *
	 * @param	string		$key
	 * @param	string		$value
	 * @return	void
	 * @global	$this->conf
	 */
	function setFlexformConfiguration($key, $value) {
		if (strpos($key, '.') !== false && $value) {
			$arrKey = t3lib_div::trimExplode('.', $key);

			for ($i = count($arrKey) - 1; $i >= 0; $i--) {
				$newValue = array();

				if ($i == count($arrKey) - 1) {
					$newValue[$arrKey[$i]] = $value;
				} else {
					$newValue[$arrKey[$i] . '.'] = $value;
				}

				$value = $newValue;
			}

			$this->conf = t3lib_div::array_merge_recursive_overrule($this->conf, $value);
		} elseif ($value) {
			$this->conf[$key] = $value;
		}
	}

	/**
	 * Ueberschreibt eventuell vorhandene TypoScript Konfigurationen oder Flexform Konfigurationen mit den Konfigurationen aus IRRE.
	 *
	 * @return	void
	 * @global	$this->conf
	 */
	function setIrreConfiguration() {
		$infoitems = 1;
		$fieldsets = 2;
		$userdeleteCounter = 0;
		$passwordconfirmationCounter = 0;
		$resendactivationCounter = 0;
		$usedfields = array();
		$requiredfields = array();
		$uniquefields = array();
		$firstkey = key($this->conf['databasefields']);

		foreach ($this->conf['databasefields'] as $position => $field) {
			// Datenbankfelder abarbeiten.
			if ($field['field']) {
				$usedfields[] = $field['field'];

				// Requiredfields erweitern.
				if ($field['required']) {
					$requiredfields[] = $field['field'];
				}

				// Uniquefields erweitern.
				if ($field['unique']) {
					$uniquefields[] = $field['field'];
				}

				// Label setzten falls angegeben.
				if ($field['label']) {
					$this->conf['_LOCAL_LANG.'][$this->getDefaultLanguage() . '.'][$field['field']] = $field['label'];
				}
			}

			// Infoitems abarbeiten.
			if (isset($field['infoitem'])) {
				$usedfields[] = '--infoitem--';

				// Falls in dem Feld etwas drinn steht.
				if ($field['infoitem']) {
					$this->conf['infoitems.'][$infoitems] = $field['infoitem'];
				}

				$infoitems++;
			}

			// Separators / Legends abarbeiten.
			if (isset($field['separator'])) {
				// Beim aller ersten Separator / Legend bloss die Legend setzten!
				if ($position == $firstkey) {
					$this->conf['legends.']['1'] = $field['separator'];
				} else {
					$usedfields[] = '--separator--';

					// Falls in dem Feld etwas drinn steht.
					if ($field['separator']) {
						$this->conf['legends.'][$fieldsets] = $field['separator'];
					}

					$fieldsets++;
				}
			}

			// Userdelete Checkbox abarbeiten.
			if (isset($field['userdelete']) && $userdeleteCounter < 1) {
				$usedfields[] = '--userdelete--';

				// Requiredfields erweitern.
				if ($field['required']) {
					$requiredfields[] = '--userdelete--';
				}

				// Label setzten falls angegeben.
				if ($field['userdelete']) {
					$this->conf['_LOCAL_LANG.'][$this->getDefaultLanguage() . '.']['userdelete'] = $field['userdelete'];
				}

				$userdeleteCounter++;
			}

			// Passwordconfirmation Feld abarbeiten.
			if (isset($field['passwordconfirmation']) && $passwordconfirmationCounter < 1) {
				$usedfields[] = '--passwordconfirmation--';

				// Label setzten falls angegeben.
				if ($field['passwordconfirmation']) {
					$this->conf['_LOCAL_LANG.'][$this->getDefaultLanguage() . '.']['passwordconfirmation'] = $field['passwordconfirmation'];
				}

				$passwordconfirmationCounter++;
			}

			// Resendactivation Feld abarbeiten.
			if (isset($field['resendactivation']) && $resendactivationCounter < 1) {
				$usedfields[] = '--resendactivation--';

				// Requiredfields erweitern.
				if ($field['required']) {
					$requiredfields[] = '--resendactivation--';
				}

				// Label setzten falls angegeben.
				if ($field['resendactivation']) {
					$this->conf['_LOCAL_LANG.'][$this->getDefaultLanguage() . '.']['resendactivation'] = $field['resendactivation'];
				}

				$resendactivationCounter++;
			}

			// Submit Button abarbeiten.
			if (isset($field['submit'])) {
				$usedfields[] = '--submit--';

				// Label setzten falls angegeben.
				if ($field['submit']) {
					$this->conf['_LOCAL_LANG.'][$this->getDefaultLanguage() . '.']['submit_' . $this->conf['showtype']] = $field['submit'];
				}
			}
		}

		// In Konfiguration uebertragen.
		$this->conf['usedfields'] = implode(',', $usedfields);
		$this->conf['requiredfields'] = implode(',', $requiredfields);
		$this->conf['uniquefields'] = implode(',', $uniquefields);
	}

	/**
	 * Gibt die komplette Validierungskonfiguration fuer die JavaScript Frontendvalidierung zurueck.
	 *
	 * @return	string		$configuration
	 */
	function getJSValidationConfiguration() {
		// Hier eine fertig generierte Konfiguration:
		// config[11]=[];
		// config[11]["username"]=[];
		// config[11]["username"]["validation"]=[];
		// config[11]["username"]["validation"]["type"]="username";
		// config[11]["username"]["valid"]="Der Benutzername darf keine Leerzeichen beinhalten!";
		// config[11]["username"]["required"]="Es muss ein Benutzername eingegeben werden!";
		// config[11]["password"]=[];
		// config[11]["password"]["validation"]=[];
		// config[11]["password"]["validation"]["type"]="password";
		// config[11]["password"]["equal"]="Es muss zwei mal das gleiche Passwort eingegeben werden!";
		// config[11]["password"]["validation"]["size"]="6";
		// config[11]["password"]["size"]="Das Passwort muss mindestens 6 Zeichen lang sein!";
		// config[11]["password"]["required"]="Es muss ein Passwort angegeben werden!";
		// inputids[11] = new Array("tx_datamintsfeuser_pi1_username", "tx_datamintsfeuser_pi1_password_1", "tx_datamintsfeuser_pi1_password_2");
		// contentid[11] = 11;

		$arrValidationFields = array();
		$configuration = 'config[' . $this->contentId . ']=[];';

		// Bei jedem Durchgang der Schliefe wird die Konfiguration fuer ein Datenbankfeld geschrieben. Ausnahmen sind hierbei Passwordfelder.
		// Gleichzeitig werden die ID's der Felder in ein Array geschrieben und am Ende zusammen gesetzt "inputids".
		foreach ($this->arrUsedFields as $fieldName) {
			if ($this->feUsersTca['columns'][$fieldName] && (is_array($this->conf['validate.'][$fieldName . '.']) || in_array($fieldName, $arrRequiredFields))) {
					if ($this->conf['validate.'][$fieldName . '.']['type'] == 'password') {
						$arrValidationFields[] = $this->extKey . '_' . $this->contentId . '_' . $fieldName;
						$arrValidationFields[] = $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_rep';
					} else {
						$arrValidationFields[] = $this->extKey . '_' . $this->contentId . '_' . $fieldName;
					}

					$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]=[];';

					if (is_array($this->conf['validate.'][$fieldName . '.'])) {
						$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["validation"]=[];';

						// Da es mehrere Validierungskonfiguration pro Feld geben kann, muss hier jede einzeln durchgelaufen werden.
						foreach ($this->conf['validate.'][$fieldName . '.'] as $key => $val) {
							if ($key == 'length') {
								$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["validation"]["size"]="' . str_replace('"', '\\"', $val) . '";';
								$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["size"]="' . str_replace('"', '\\"', $this->getLabel($fieldName . '_error_length')) . '";';
							} elseif ($key == 'regexp') {
								// Da In JavaScript die regulaeren Ausdruecke nicht in einem String vorkommen duerfen diese entsprechen konvertieren (Slash am Anfang und am Ende).
								// Um Fehler im regulaeren Ausdruck zu vermeiden, werden hier alle Slashes entfernt, "\/" wird debei nicht beruecksichtigt!
								// Als erstes den hinteren Slash entfernen und den eventuell vorhandenen Modifier merken.
								$matches = array();

								if (preg_match("/\/[a-z]*$/", $val, $matches)) {
									$regexpEnd = substr($val, - strlen($matches[0]));
									$val = substr($val, 0, strlen($val) - strlen($matches[0]));
								} else {
									$regexpEnd = '/';
								}

								// Einen eventuell vorhandenen Slash am Anfang ebenfalls entfernen.
								$regexpStart = '/';

								if (preg_match("/^\//", $val)) {
									$val = substr($val, 1);
								}

								// Dann alle Slashes aus dem String entfernen, unter beruecksichtigung von "\/"!
								$val = preg_replace('/([^\\\])\//', '$1', $val);
								$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["validation"]["' . $key . '"]=' . $regexpStart . $val . $regexpEnd . ';';
							} else {
								$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["validation"]["' . $key . '"]="' . str_replace('"', '\\"', $val) . '";';
							}

							if ($key == 'type' && $val == 'password') {
								$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["equal"]="' . str_replace('"', '\\"', $this->getLabel($fieldName . '_error_equal')) . '";';
							}
						}

						if ($this->conf['validate.'][$fieldName . '.']['type'] != 'password') {
							$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["valid"]="' . str_replace('"', '\\"', $this->getLabel($fieldName . '_error_valid')) . '";';
						}
					}

					if (in_array($fieldName, $this->arrRequiredFields)) {
						$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["required"]="' . str_replace('"', '\\"', $this->getLabel($fieldName . '_error_required')) . '";';
					}
			}
		}

		$configuration .= 'inputids[' . $this->contentId . ']=new Array("' . implode('","', $arrValidationFields) . '");contentids[' . $this->contentId . ']=' . $this->contentId . ';';

		return $configuration;
	}

	/**
	 * Ueberschreibt eventuell vorhandene TCA Konfiguration mit TypoScript Konfiguration.
	 *
	 * @return	void
	 * @global	$this->feUsersTca
	 */
	function getFeUsersTca() {
		$GLOBALS['TSFE']->includeTCA();
		$this->feUsersTca = $GLOBALS['TCA']['fe_users'];
		if ($this->conf['fieldconfig.']) {
			$this->feUsersTca['columns'] = t3lib_div::array_merge_recursive_overrule((array)$this->feUsersTca['columns'], (array)$this->deletePointInArrayKey($this->conf['fieldconfig.']));
		}
	}

	/**
	 * Ermittelt die General Record Storage Pid bzw. den vom User festgelegten Userfolder.
	 *
	 * @return	void
	 * @global	$this->storagePid
	 */
	function getStoragePid() {
		$this->storagePid = $this->conf['register.']['userfolder'];
		if (!$this->storagePid) {
			$arrayRootPids = $GLOBALS['TSFE']->getStorageSiterootPids();
			$this->storagePid = $arrayRootPids['_STORAGE_PID'];
		}
	}

	/**
	 * Loescht den Punkt den Typo3 bei TypoScript-Variablen (Arrays) hinzufuegt.
	 *
	 * @param	array		$array
	 * @return	array		$newArray
	 */
	function deletePointInArrayKey($array) {
		// Neues Array erstellen um das alte Array nicht zu ueberschreiben.
		$newArray = array();

		// Alle Elemente des Arrays durchgehen.
		foreach ($array as $key => $val) {
			if (is_array($val)) {
				// Wenn der Inhalt des Elements ein Array ist, letztes Zeichen entfernen (Punkt).
				$newKey = substr($key, 0, -1);

				// Da das Array recursiv sein kann Funktion erneut ausfuehren.
				$newVal = $this->deletePointInArrayKey($val);
			} else {
				// Wenn Element kein Array ist, dann einfach Key und Value uebernehmen.
				$newKey = $key;
				$newVal = $val;
			}

			// Neues Array fuellen.
			$newArray[$newKey] = $newVal;
		}

		return $newArray;
	}

	/**
	 * Checks if a string is utf8 encoded or not.
	 *
	 * @param	string		$str
	 * @return	boolean
	 */
	function checkUtf8($str) {
		$len = strlen($str);
		for ($i = 0; $i < $len; $i++) {
			$c = ord($str[$i]);

			if ($c > 128) {
				if (($c > 247)) {
					return false;
				} elseif ($c > 239) {
					$bytes = 4;
				} elseif ($c > 223) {
					$bytes = 3;
				} elseif ($c > 191) {
					$bytes = 2;
				} else {
					return false;
				}

				if (($i + $bytes) > $len) {
					return false;
				}

				while ($bytes > 1) {
					$i++;
					$b = ord($str[$i]);

					if ($b < 128 || $b > 191) {
						return false;
					}

					$bytes--;
				}
			}
		}

		return true;
	}

	/**
	 * Cleans an array from all empty elements.
	 *
	 * @param	string		$array
	 * @return	string
	 */
	function cleanArray($array) {
		foreach ($array as $key => $val) {
			if (!$val) {
				unset($array[$key]);
			}
		}

		return $array;
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/pi1/class.tx_datamintsfeuser_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/pi1/class.tx_datamintsfeuser_pi1.php']);
}

?>