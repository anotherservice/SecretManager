<?php

include_once( 'Constants.inc.php' );

include_once( IICA_LIBRARIES . '/Class_IICA_Parameters_PDO.inc.php' );

class Backup extends IICA_Parameters {
/**
* Cette classe gère les sauvegardes du SecretManager.
*
* PHP version 5
* @license http://www.gnu.org/copyleft/lesser.html  LGPL License 3
* @author Pierre-Luc MARY
*/
	public function __construct() {
	/**
	* Connexion à la base de données.
	*
	* @license http://www.gnu.org/copyleft/lesser.html  LGPL License 3
	* @author Pierre-Luc MARY
	* @date 2013-11-25
	*
	* @return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}


	/* ===============================================================================
	** Gestion des Sauvegardes
	*/
	
	public function backup_secrets() {
	/**
	* Sauvegarde les Secrets (scr_secrets, sgr_secrets_groups, stp_secret_types, env_environments).
	*
	* @license http://www.gnu.org/copyleft/lesser.html  LGPL License 3
	* @author Pierre-Luc MARY
	* @date 2013-11-25
	*
	* @return Renvoi la date de sauvegarde c'est bien passé, sinon "FALSE".
	*/
		$Save_Date = date( 'Y-m-d_H.i.s' );
		$Save_Date_1 = str_replace( '_', ' ', $Save_Date );
		$Save_Date_1 = str_replace( '.', ':', $Save_Date_1 );

		$Save_Filename = DIR_BACKUP . '/secrets_' . $Save_Date . '.xml';
		
		// Création du fichier cible.
		if ( ! $Save_File = @fopen( $Save_Filename, 'w' ) ) {
		    throw new Exception( '% L_ERROR_OPEN, create file error "' . $Save_Filename . '"', -10 );
		}
		
        // Ecriture de l'entête.
		fwrite( $Save_File, '<?xml version="1.0" encoding="utf-8"?>' . "\n" .
		    '<secrets date="' . $Save_Date_1 . '">' . "\n" ); 
		    

        // ============================================
        // Traitement de la table "Goupes de Secrets".		    
        if ( ! $Result = $this->prepare( 'SELECT sgr_id, sgr_label, sgr_alert ' .
            'FROM sgr_secrets_groups' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, ' <table id="sgr" name="sgr_secrets_groups">' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="sgr-' . $Row_Count . '">' . "\n" .
		        '   <column name="sgr_id">' . $Occurrence->sgr_id . '</column>' . "\n" .
		        '   <column name="sgr_label">' . $Occurrence->sgr_label . '</column>' . "\n" .
		        '   <column name="sgr_alert">' . $Occurrence->sgr_alert . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );


        // ============================================
        // Traitement de la table "Types de Secrets".		    
        if ( ! $Result = $this->prepare( 'SELECT stp_id, stp_name ' .
            'FROM stp_secret_types' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, "\n" . ' <table id="stp" name="stp_secret_types">' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="stp-' . $Row_Count . '">' . "\n" .
		        '   <column name="stp_id">' . $Occurrence->stp_id . '</column>' . "\n" .
		        '   <column name="stp_name">' . $Occurrence->stp_name . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );


        // ============================================
        // Traitement de la table "Environnements d'un Secret".		    
        if ( ! $Result = $this->prepare( 'SELECT env_id, env_name ' .
            'FROM env_environments' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, "\n" . ' <table id="env" name="env_environments">' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="env-' . $Row_Count . '">' . "\n" .
		        '   <column name="env_id">' . $Occurrence->env_id . '</column>' . "\n" .
		        '   <column name="env_name">' . $Occurrence->env_name . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );


        // ============================================
        // Traitement de la table des "Secrets".		    
        if ( ! $Result = $this->prepare( 'SELECT ' . 
            'scr_id, sgr_id, stp_id, env_id, ' .
            'scr_host, scr_user, scr_password, scr_application, scr_comment, scr_alert, ' .
            'scr_creation_date, scr_modification_date, scr_expiration_date ' .
            'FROM scr_secrets' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, "\n" . ' <table id="scr" name="scr_secrets">' . "\n" .
		    '  <key id="mother_key">' . file_get_contents( DIR_LIBRARIES . '/secret.dat' ) . '</key>' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="scr-' . $Row_Count . '">' . "\n" .
		        '   <column name="scr_id">' . $Occurrence->scr_id . '</column>' . "\n" .
		        '   <column name="stp_id">' . $Occurrence->stp_id . '</column>' . "\n" .
		        '   <column name="env_id">' . $Occurrence->env_id . '</column>' . "\n" .
		        '   <column name="scr_host">' . $Occurrence->scr_host . '</column>' . "\n" .
		        '   <column name="scr_user">' . $Occurrence->scr_user . '</column>' . "\n" .
		        '   <column name="scr_password">' . $Occurrence->scr_password . '</column>' . "\n" .
		        '   <column name="scr_application">' . $Occurrence->scr_application . '</column>' . "\n" .
		        '   <column name="scr_comment">' . $Occurrence->scr_comment . '</column>' . "\n" .
		        '   <column name="scr_alert">' . $Occurrence->scr_alert . '</column>' . "\n" .
		        '   <column name="scr_creation_date">' . $Occurrence->scr_creation_date . '</column>' . "\n" .
		        '   <column name="scr_modification_date">' . $Occurrence->scr_modification_date . '</column>' . "\n" .
		        '   <column name="scr_expiration_date">' . $Occurrence->scr_expiration_date . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );


        // =======================================
        // Pied de page du fichier de sauvegarde.
        fwrite( $Save_File, '</secrets>' . "\n" );

        fclose( $Save_File );
		
		return $Save_Date_1;
	}

    // ==================================================
    // **************************************************
    // ==================================================

	public function backup_total() {
	/**
	* Sauvegarde total des données de SecretManager.
	*
	* @license http://www.gnu.org/copyleft/lesser.html  LGPL License 3
	* @author Pierre-Luc MARY
	* @date 2013-11-25
	*
	* @return Renvoi la date de sauvegarde c'est bien passé, sinon "FALSE".
	*/
		$Save_Date = date( 'Y-m-d_H.i.s' );
		$Save_Date_1 = str_replace( '_', ' ', $Save_Date );
		$Save_Date_1 = str_replace( '.', ':', $Save_Date_1 );

		$Save_Filename = DIR_BACKUP . '/total_' . $Save_Date . '.xml';
		
		// Création du fichier cible.
		if ( ! $Save_File = @fopen( $Save_Filename, 'w' ) ) {
		    throw new Exception( '% L_ERROR_OPEN, create file error "' . $Save_Filename . '"', -10 );
		}
		
        // Ecriture de l'entête.
		fwrite( $Save_File, '<?xml version="1.0" encoding="utf-8"?>' . "\n" .
		    '<secrets date="' . $Save_Date_1 . '">' . "\n" ); 
		    

        // ============================================
        // Traitement de la table de "l'Historique des accès aux Secrets".		    
        if ( ! $Result = $this->prepare( 'SELECT ach_id, scr_id, idn_id, ach_date, '.
            'ach_access, ach_ip, aht_id ' .
            'FROM ach_access_history' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, ' <table id="ach" name="ach_access_history">' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="ach-' . $Row_Count . '">' . "\n" .
		        '   <column name="ach_id">' . $Occurrence->ach_id . '</column>' . "\n" .
		        '   <column name="scr_id">' . $Occurrence->scr_id . '</column>' . "\n" .
		        '   <column name="idn_id">' . $Occurrence->idn_id . '</column>' . "\n" .
		        '   <column name="ach_date">' . $Occurrence->ach_date . '</column>' . "\n" .
		        '   <column name="ach_access">' . htmlspecialchars($Occurrence->ach_access) . '</column>' . "\n" .
		        '   <column name="ach_ip">' . $Occurrence->ach_ip . '</column>' . "\n" .
		        '   <column name="aht_id">' . $Occurrence->aht_id . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );
		    

        // ============================================
        // Traitement de la table des "Civilités".
        if ( ! $Result = $this->prepare( 'SELECT cvl_id, cvl_last_name, cvl_first_name, ' .
            'cvl_sex, cvl_birth_date, cvl_born_town ' .
            'FROM cvl_civilities' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, ' <table id="cvl" name="cvl_civilities">' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="cvl-' . $Row_Count . '">' . "\n" .
		        '   <column name="cvl_id">' . $Occurrence->cvl_id . '</column>' . "\n" .
		        '   <column name="cvl_last_name">' . $Occurrence->cvl_last_name . '</column>' . "\n" .
		        '   <column name="cvl_first_name">' . $Occurrence->cvl_first_name . '</column>' . "\n" .
		        '   <column name="cvl_sex">' . $Occurrence->cvl_sex . '</column>' . "\n" .
		        '   <column name="cvl_birth_date">' . $Occurrence->cvl_birth_date . '</column>' . "\n" .
		        '   <column name="cvl_born_town">' . $Occurrence->cvl_born_town . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );
		    

        // ============================================
        // Traitement de la table des "Entités".
        if ( ! $Result = $this->prepare( 'SELECT ent_id, ent_code, ent_label ' .
            'FROM ent_entities ' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, ' <table id="ent" name="ent_entities">' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="ent-' . $Row_Count . '">' . "\n" .
		        '   <column name="ent_id">' . $Occurrence->ent_id . '</column>' . "\n" .
		        '   <column name="ent_code">' . $Occurrence->ent_code . '</column>' . "\n" .
		        '   <column name="ent_label">' . $Occurrence->ent_label . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );
		    

        // ============================================
        // Traitement de la table des "Environnements".
        if ( ! $Result = $this->prepare( 'SELECT env_id, env_name ' .
            'FROM env_environments ' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, ' <table id="env" name="env_environments">' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="env-' . $Row_Count . '">' . "\n" .
		        '   <column name="env_id">' . $Occurrence->env_id . '</column>' . "\n" .
		        '   <column name="env_name">' . $Occurrence->env_name . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );
		    

        // ============================================
        // Traitement de la table des "Identités".
        if ( ! $Result = $this->prepare( 'SELECT idn_id, ent_id, cvl_id, idn_login, ' .
            'idn_authenticator, idn_salt, idn_change_authenticator, idn_super_admin, ' .
            'idn_attempt, idn_disable, idn_last_connection, idn_expiration_date, ' .
            'idn_updated_authentication ' .
            'FROM idn_identities ' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, ' <table id="idn" name="idn_identities">' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="idn-' . $Row_Count . '">' . "\n" .
		        '   <column name="idn_id">' . $Occurrence->idn_id . '</column>' . "\n" .
		        '   <column name="ent_id">' . $Occurrence->ent_id . '</column>' . "\n" .
		        '   <column name="cvl_id">' . $Occurrence->cvl_id . '</column>' . "\n" .
		        '   <column name="idn_login">' . $Occurrence->idn_login . '</column>' . "\n" .
		        '   <column name="idn_authenticator">' . $Occurrence->idn_authenticator . '</column>' . "\n" .
		        '   <column name="idn_salt">' . $Occurrence->idn_salt . '</column>' . "\n" .
		        '   <column name="idn_change_authenticator">' . $Occurrence->idn_change_authenticator . '</column>' . "\n" .
		        '   <column name="idn_super_admin">' . $Occurrence->idn_super_admin . '</column>' . "\n" .
		        '   <column name="idn_attempt">' . $Occurrence->idn_attempt . '</column>' . "\n" .
		        '   <column name="idn_disable">' . $Occurrence->idn_disable . '</column>' . "\n" .
		        '   <column name="idn_last_connection">' . $Occurrence->idn_last_connection . '</column>' . "\n" .
		        '   <column name="idn_expiration_date">' . $Occurrence->idn_expiration_date . '</column>' . "\n" .
		        '   <column name="idn_updated_authentication">' . $Occurrence->idn_updated_authentication . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );


        // ============================================
        // Traitement de la table des "Identités associées des Profils".
        if ( ! $Result = $this->prepare( 'SELECT idn_id, prf_id ' .
            'FROM idpr_identities_profiles ' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, ' <table id="idpr" name="idpr_identities_profiles">' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="idpr-' . $Row_Count . '">' . "\n" .
		        '   <column name="idn_id">' . $Occurrence->idn_id . '</column>' . "\n" .
		        '   <column name="prf_id">' . $Occurrence->prf_id . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );


        // ============================================
        // Traitement de la table des "Profils".
        if ( ! $Result = $this->prepare( 'SELECT prf_id, prf_label ' .
            'FROM prf_profiles ' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, ' <table id="prf" name="prf_profiles">' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="prf-' . $Row_Count . '">' . "\n" .
		        '   <column name="prf_id">' . $Occurrence->prf_id . '</column>' . "\n" .
		        '   <column name="prf_label">' . $Occurrence->prf_label . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );


        // ============================================
        // Traitement de la table des "Profils associées des Groupes de Secrets".
        if ( ! $Result = $this->prepare( 'SELECT prf_id, sgr_id, rgh_id ' .
            'FROM prsg_profiles_secrets_groups ' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, ' <table id="prsg" name="prsg_profiles_secrets_groups">' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="prsg-' . $Row_Count . '">' . "\n" .
		        '   <column name="prf_id">' . $Occurrence->prf_id . '</column>' . "\n" .
		        '   <column name="sgr_id">' . $Occurrence->sgr_id . '</column>' . "\n" .
		        '   <column name="rgh_id">' . $Occurrence->rgh_id . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );


        // ============================================
        // Traitement de la table des "Droits".
        if ( ! $Result = $this->prepare( 'SELECT rgh_id, rgh_name ' .
            'FROM rgh_rights ' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, ' <table id="rgh" name="rgh_rights">' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="rgh-' . $Row_Count . '">' . "\n" .
		        '   <column name="rgh_id">' . $Occurrence->rgh_id . '</column>' . "\n" .
		        '   <column name="rgh_name">' . $Occurrence->rgh_name . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );
		    

        // ============================================
        // Traitement de la table "Goupes de Secrets".		    
        if ( ! $Result = $this->prepare( 'SELECT sgr_id, sgr_label, sgr_alert ' .
            'FROM sgr_secrets_groups' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, ' <table id="sgr" name="sgr_secrets_groups">' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="sgr-' . $Row_Count . '">' . "\n" .
		        '   <column name="sgr_id">' . $Occurrence->sgr_id . '</column>' . "\n" .
		        '   <column name="sgr_label">' . $Occurrence->sgr_label . '</column>' . "\n" .
		        '   <column name="sgr_alert">' . $Occurrence->sgr_alert . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );


        // ============================================
        // Traitement de la table "Types de Secrets".		    
        if ( ! $Result = $this->prepare( 'SELECT stp_id, stp_name ' .
            'FROM stp_secret_types' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, "\n" . ' <table id="stp" name="stp_secret_types">' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="stp-' . $Row_Count . '">' . "\n" .
		        '   <column name="stp_id">' . $Occurrence->stp_id . '</column>' . "\n" .
		        '   <column name="stp_name">' . $Occurrence->stp_name . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );


        // ============================================
        // Traitement de la table "Paramètres du Système".		    
        if ( ! $Result = $this->prepare( 'SELECT spr_name, spr_value ' .
            'FROM spr_system_parameters' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, "\n" . ' <table id="spr" name="spr_system_parameters">' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="spr-' . $Row_Count . '">' . "\n" .
		        '   <column name="spr_name">' . $Occurrence->spr_name . '</column>' . "\n" .
		        '   <column name="spr_value">' . $Occurrence->spr_value . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );


        // ============================================
        // Traitement de la table des "Secrets".		    
        if ( ! $Result = $this->prepare( 'SELECT ' . 
            'scr_id, sgr_id, stp_id, env_id, ' .
            'scr_host, scr_user, scr_password, scr_application, scr_comment, scr_alert, ' .
            'scr_creation_date, scr_modification_date, scr_expiration_date ' .
            'FROM scr_secrets' ) ) {
            $Error = $Result->errorInfo();
            throw new Exception( $Error[ 2 ], $Error[ 1 ] );
        }

		if ( ! $Result->execute() ) {
			$Error = $Result->errorInfo();
			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}
		
		fwrite( $Save_File, "\n" . ' <table id="scr" name="scr_secrets">' . "\n" .
		    '  <key id="mother_key">' . file_get_contents( DIR_LIBRARIES . '/secret.dat' ) . '</key>' . "\n" );
		        
        $Row_Count = 0;
        
		while ( $Occurrence = $Result->fetchObject() ) {
		    $Row_Count += 1;
		    
		    $Out_Occurrence = '  <row id="scr-' . $Row_Count . '">' . "\n" .
		        '   <column name="scr_id">' . $Occurrence->scr_id . '</column>' . "\n" .
		        '   <column name="stp_id">' . $Occurrence->stp_id . '</column>' . "\n" .
		        '   <column name="env_id">' . $Occurrence->env_id . '</column>' . "\n" .
		        '   <column name="scr_host">' . $Occurrence->scr_host . '</column>' . "\n" .
		        '   <column name="scr_user">' . $Occurrence->scr_user . '</column>' . "\n" .
		        '   <column name="scr_password">' . $Occurrence->scr_password . '</column>' . "\n" .
		        '   <column name="scr_application">' . $Occurrence->scr_application . '</column>' . "\n" .
		        '   <column name="scr_comment">' . $Occurrence->scr_comment . '</column>' . "\n" .
		        '   <column name="scr_alert">' . $Occurrence->scr_alert . '</column>' . "\n" .
		        '   <column name="scr_creation_date">' . $Occurrence->scr_creation_date . '</column>' . "\n" .
		        '   <column name="scr_modification_date">' . $Occurrence->scr_modification_date . '</column>' . "\n" .
		        '   <column name="scr_expiration_date">' . $Occurrence->scr_expiration_date . '</column>' . "\n" .
                '  </row>' . "\n" ;

            fwrite( $Save_File, $Out_Occurrence );
		}

        fwrite( $Save_File, ' </table>' . "\n" );


        // =======================================
        // Pied de page du fichier de sauvegarde.
        fwrite( $Save_File, '</secrets>' . "\n" );

        fclose( $Save_File );
		
		return $Save_Date_1;
	}

} // Fin class Backup

?>