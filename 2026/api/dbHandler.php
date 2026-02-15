<?php
/*
  MySQL database handler
*/
class dbHandler
{
  private $dbIniFile = "../../../../db_config.ini";
  private $charset = "utf8";
  private $conn = null;
  private $alreadyConnected = false;
  private $configKeys = array(
    "server",
    "db",
    "username",
    "password",
    "eventcode",
    "tbakey",
    "datatable",
    "tbatable",
    "pittable",
    "strategictable",
    "scouttable",
    "aliastable",
    "watchtable",
    "hoppercaptable",
    "useP",
    "useQm",
    "useSf",
    "useF"
  );

  private $opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
  ];

  // Connect to the database
  public function connectToDB()
  {
    if (!$this->alreadyConnected)
    {
      $dbConfig = $this->readDbConfig();
      $dsn = "mysql:host=" . $dbConfig["server"] . ";dbname=" . $dbConfig["db"] . ";charset=" . $this->charset;
      try
      {
        $this->conn = new PDO($dsn, $dbConfig["username"], $dbConfig["password"], $this->opt);
        $this->alreadyConnected = true;
      }
      catch (PDOException $e)
      {
        error_log($e);
      }
    }
    return ($this->conn);
  }

  // Connect to the server holding the database
  private function connectToServer()
  {
    $dbConfig = $this->readDbConfig();
    $dsn = "mysql:host=" . $dbConfig["server"] . ";charset=" . $this->charset;

    try
    {
      $this->conn = new PDO($dsn, $dbConfig["username"], $dbConfig["password"], $this->opt);
      $this->alreadyConnected = true;
    }
    catch (PDOException $e)
    {
      error_log($e);
    }

    return $this->conn;
  }

  ////////////////////////////////////////////////////////
  ////////////////////// Match Data //////////////////////
  ////////////////////////////////////////////////////////

  // Write match data row into table
  public function writeRowToMatchTable($mData)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "INSERT INTO " . $dbConfig["datatable"] .
      "(
        entrykey,
        eventcode,
        matchnumber,
        teamnumber,
        teamalias,
        scoutname,
        died,
        autonShootPreload,
        autonPreloadAccuracy,
        autonHoppersShot,
        autonHopperAccuracy,
        autonAllianceZone,
        autonDepot,
        autonOutpost,
        autonNeutralZone,
        autonClimb,
        teleopHoppersUsed,
        teleopHopperAccuracy,
        teleopIntakeAndShoot,
        teleopNeutralToAlliance,
        teleopAllianceToAlliance,
        teleopPassingRate,
        teleopDefenseLevel,
        endgameCageClimb,
        endgameStartClimb,
        comment
      )
      VALUES
      (
        :entrykey,
        :eventcode,
        :matchnumber,
        :teamnumber,
        :teamalias,
        :scoutname,
        :died,
        :autonShootPreload,
        :autonPreloadAccuracy,
        :autonHoppersShot,
        :autonHopperAccuracy,
        :autonAllianceZone,
        :autonDepot,
        :autonOutpost,
        :autonNeutralZone,
        :autonClimb,
        :teleopHoppersUsed,
        :teleopHopperAccuracy,
        :teleopIntakeAndShoot,
        :teleopNeutralToAlliance,
        :teleopAllianceToAlliance,
        :teleopPassingRate,
        :teleopDefenseLevel,
        :endgameCageClimb,
        :endgameStartClimb,
        :comment
      )";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute($mData);
  }

  // Enforce integer data type
  private function enforceInt($val)
  {
    return intval($val);
  }

  // Enforce data typing for match data
  private function enforceDataTyping($mData)
  {
    $out = array();
    foreach ($mData as $row)
    {
      foreach ($row as $key => $value)
      {
        if ($key === "autonShootPreload" || $key === "autonPreloadAccuracy" ||
            $key === "autonHoppersShot" || $key === "autonHopperAccuracy" || $key === "autonAllianceZone" || $key === "autonDepot" ||
            $key === "autonOutpost" || $key === "autonNeutralZone" ||
            $key === "autonClimb" || $key === "teleopHoppersUsed" ||
            $key === "teleopHopperAccuracy" || $key === "teleopIntakeAndShoot" || $key === "teleopNeutralToAlliance" || $key === "teleopAllianceToAlliance" ||
            $key === "teleopPassingRate" || $key === "teleopDefenseLevel" ||
            $key === "endgameCageClimb" || $key === "endgameStartClimb" || $key === "died" || $key === "teamalias"
        )
        {
          // verifying and enforcing all keys above to be an integer
          $row[$key] = $this->enforceInt($value);
        }
        else
        {
          // all other keys use the current value
          $row[$key] = $value;
        }
      }
      array_push($out, $row);
    }
    return $out;
  }

  // Read all match data for event
  public function readAllFromMatchTable($eventCode)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "SELECT 
        entrykey,
        eventcode,
        matchnumber,
        teamnumber,
        teamalias,
        scoutname,
        died,
        autonShootPreload,
        autonPreloadAccuracy,
        autonHoppersShot,
        autonHopperAccuracy,
        autonAllianceZone,
        autonDepot,
        autonOutpost,
        autonNeutralZone,
        autonClimb,
        teleopHoppersUsed,
        teleopHopperAccuracy,
        teleopIntakeAndShoot,
        teleopNeutralToAlliance,
        teleopAllianceToAlliance,
        teleopPassingRate,
        teleopDefenseLevel,
        endgameCageClimb,
        endgameStartClimb,
        comment
        FROM " . $dbConfig["datatable"] .
      " WHERE eventcode='" . $eventCode . "'";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute();
    $result = $prepared_statement->fetchAll();
    return $this->enforceDataTyping($result);
  }

  // Read match data for specific team in event
  public function readTeamFromMatchTable($teamNumber, $eventCode)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "SELECT 
        entrykey,
        eventcode,
        matchnumber,
        teamnumber,
        teamalias,
        scoutname,
        died,
        autonShootPreload,
        autonPreloadAccuracy,
        autonHoppersShot,
        autonHopperAccuracy,
        autonAllianceZone,
        autonDepot,
        autonOutpost,
        autonNeutralZone,
        autonClimb,
        teleopHoppersUsed,
        teleopHopperAccuracy,
        teleopIntakeAndShoot,
        teleopNeutralToAlliance,
        teleopAllianceToAlliance,
        teleopPassingRate,
        teleopDefenseLevel,
        endgameCageClimb,
        endgameStartClimb,
        comment
        FROM " . $dbConfig["datatable"] .
      " WHERE eventcode='" . $eventCode . "' AND teamnumber='" . $teamNumber . "'";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute();
    $result = $prepared_statement->fetchAll();
    return $this->enforceDataTyping($result);
  }

  ////////////////////////////////////////////////////////
  ////////////////////// Pit Data ////////////////////////
  ////////////////////////////////////////////////////////

  // Write pit data row into table
  public function writeRowToPitTable($pData)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "INSERT INTO " . $dbConfig["pittable"] .
      "(
        entrykey,
        eventcode,
        teamnumber,
        scoutname,
        swerve,
        drivemotors,
        spareparts,
        proglanguage,
        computervision,
        pitorg,
        preparedness,
        numbatteries,
        caphopper,
        trenchdrive,
        climbable,
        climblevel
      )
      VALUES
      (
        :entrykey,
        :eventcode,
        :teamnumber,
        :scoutname,
        :swerve,
        :drivemotors,
        :spareparts,
        :proglanguage,
        :computervision,
        :pitorg,
        :preparedness,
        :numbatteries,
        :caphopper,
        :trenchdrive,
        :climbable,
        :climblevel
      )";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute($pData);
  }

  // Read all pit data for event
  public function readAllPitTable($eventCode)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "SELECT 
        teamnumber,
        scoutname,
        swerve,
        drivemotors,
        spareparts,
        proglanguage,
        computervision,
        numbatteries,
        caphopper,
        pitorg,
        preparedness,
        numbatteries,
        caphopper,
        trenchdrive,
        climbable,
        climblevel
        FROM " . $dbConfig["pittable"] .
      " WHERE eventcode='" . $eventCode . "'";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute();
    $result = $prepared_statement->fetchAll();
    return $result;
  }

  ////////////////////////////////////////////////////////
  ////////////////////// Strategic Data //////////////////
  ////////////////////////////////////////////////////////

  // Write strategic data row into table
  public function writeRowToStrategicTable($sData)
  {
    $dbConfig = $this->readDbConfig();

    $sql = "INSERT INTO " . $dbConfig["strategictable"] .
      "(
        entrykey,
        eventcode,
        matchnumber,
        teamnumber,
        scoutname,
        activeShiftLoadedHopper,
        activeShiftShotHopper,
        activeShiftPassingFromAlliance,
        activeShiftPassingFromNeutral,
        activeShiftDefenseAgainstShooter,
        activeShiftDefenseAtBump,
        activeShiftDefenseAtTrench,
        inactiveShiftLoadedHopper,
        inactiveShiftShotHopper,
        inactiveShiftPassingFromAlliance,
        inactiveShiftPassingFromNeutral,
        inactiveShiftDefenseAgainstShooter,
        inactiveShiftDefenseAtBump,
        inactiveShiftDefenseAtTrench,
        againstDefenseEffectiveness,
        bumpTippedOver,
        bumpBottomedOut,
        bumpAvoidedDefender,
        bumpGotStuckOnFuel,
        fouls,
        problem_comment,
        general_comment
      )
      VALUES
      (
        :entrykey,
        :eventcode,
        :matchnumber,
        :teamnumber,
        :scoutname,
        :activeShiftLoadedHopper,
        :activeShiftShotHopper,
        :activeShiftPassingFromAlliance,
        :activeShiftPassingFromNeutral,
        :activeShiftDefenseAgainstShooter,
        :activeShiftDefenseAtBump,
        :activeShiftDefenseAtTrench,
        :inactiveShiftLoadedHopper,
        :inactiveShiftShotHopper,
        :inactiveShiftPassingFromAlliance,
        :inactiveShiftPassingFromNeutral,
        :inactiveShiftDefenseAgainstShooter,
        :inactiveShiftDefenseAtBump,
        :inactiveShiftDefenseAtTrench,
        :againstDefenseEffectiveness,
        :bumpTippedOver,
        :bumpBottomedOut,
        :bumpAvoidedDefender,
        :bumpGotStuckOnFuel,
        :fouls,
        :problem_comment,
        :general_comment
      )";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute($sData);
  }

  // Read all strategic data for event
  public function readAllFromStrategicTable($eventCode)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "SELECT 
        matchnumber,
        teamnumber,
        scoutname,
        activeShiftLoadedHopper,
        activeShiftShotHopper,
        activeShiftPassingFromAlliance,
        activeShiftPassingFromNeutral,
        activeShiftDefenseAgainstShooter,
        activeShiftDefenseAtBump,
        activeShiftDefenseAtTrench,
        inactiveShiftLoadedHopper,
        inactiveShiftShotHopper,
        inactiveShiftPassingFromAlliance,
        inactiveShiftPassingFromNeutral,
        inactiveShiftDefenseAgainstShooter,
        inactiveShiftDefenseAtBump,
        inactiveShiftDefenseAtTrench,
        againstDefenseEffectiveness,
        bumpTippedOver,
        bumpBottomedOut,
        bumpAvoidedDefender,
        bumpGotStuckOnFuel,
        fouls,
        problem_comment,
        general_comment 
        FROM " . $dbConfig["strategictable"] .
      " WHERE eventcode='" . $eventCode . "'";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute();
    $result = $prepared_statement->fetchAll();
    return $result;
  }

  // Read strategic data for specific team in event
  public function readTeamFromStrategicTable($teamNumber, $eventCode)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "SELECT 
        matchnumber,
        teamnumber,
        scoutname,
        activeShiftLoadedHopper,
        activeShiftShotHopper,
        activeShiftPassingFromAlliance,
        activeShiftPassingFromNeutral,
        activeShiftDefenseAgainstShooter,
        activeShiftDefenseAtBump,
        activeShiftDefenseAtTrench,
        inactiveShiftLoadedHopper,
        inactiveShiftShotHopper,
        inactiveShiftPassingFromAlliance,
        inactiveShiftPassingFromNeutral,
        inactiveShiftDefenseAgainstShooter,
        inactiveShiftDefenseAtBump,
        inactiveShiftDefenseAtTrench,
        againstDefenseEffectiveness,
        bumpTippedOver,
        bumpBottomedOut,
        bumpAvoidedDefender,
        bumpGotStuckOnFuel,
        fouls,
        problem_comment,
        general_comment 
        FROM " . $dbConfig["strategictable"] .
      " WHERE eventcode='" . $eventCode . "' AND teamnumber='" . $teamNumber . "'";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute();
    $result = $prepared_statement->fetchAll();
    return $result;
  }

  ////////////////////////////////////////////////////////
  ////////////////////// Scout Name //////////////////////
  ////////////////////////////////////////////////////////

  // Write scout name record into table and replace if an entry already exists
  public function writeScoutNameToTable($sName)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "INSERT INTO " . $dbConfig["scouttable"] .
      "(
        entrykey,
        eventcode,
        scoutname
      )
      VALUES
      (
        :entrykey,
        :eventcode,
        :scoutname
      )";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute($sName);
  }

  //
  public function deleteScoutNameFromTable($sName)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "DELETE FROM " . $dbConfig["scouttable"] . " WHERE entrykey='" . $sName["entrykey"] . "'";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute();
  }

  //
  public function readEventScoutTable($eventCode)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "SELECT 
        entrykey,
        eventcode,
        scoutname
        FROM " . $dbConfig["scouttable"] .
      " WHERE eventcode='" . $eventCode . "'";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute();
    $result = $prepared_statement->fetchAll();
    return $result;
  }

  ////////////////////////////////////////////////////////
  ////////////////////// Hopper Caps Table////////////////
  ////////////////////////////////////////////////////////

  // Write Hopper Capacity record into table and replace if an entry already exists
  public function writeHopperCapToTable($hCap)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "REPLACE INTO " . $dbConfig["hoppercaptable"] .
      "(
        entrykey,
        eventcode,
        teamnumber,
        hoppercap
      )
      VALUES
      (
        :entrykey,
        :eventcode,
        :teamnumber,
        :hoppercap
      )";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute($hCap);
  }

  // Delete hopper cap record from table
  public function deleteHopperCapFromTable($hCap)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "DELETE FROM " . $dbConfig["hoppercaptable"] . " WHERE entrykey='" . $hCap["entrykey"] . "'";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute();
  }

  // Read all hopper cap records for event
  public function readEventHopperCapTable($eventCode)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "SELECT 
        entrykey,
        eventcode,
        teamnumber,
        hoppercap
        FROM " . $dbConfig["hoppercaptable"] .
      " WHERE eventcode='" . $eventCode . "'";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute();
    $result = $prepared_statement->fetchAll();
    return $result;
  }

  ////////////////////////////////////////////////////////
  ////////////////////// Team Alias //////////////////////
  ////////////////////////////////////////////////////////

  // Write team alias record into table and replace if an entry already exists
  public function writeAliasNumberToTable($aNum)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "REPLACE INTO " . $dbConfig["aliastable"] .
      "(
        entrykey,
        eventcode,
        teamnumber,
        aliasnumber
      )
      VALUES
      (
        :entrykey,
        :eventcode,
        :teamnumber,
        :aliasnumber
      )";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute($aNum);
  }

  // Delete team alias record from table
  public function deleteTeamAliasFromTable($aNum)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "DELETE FROM " . $dbConfig["aliastable"] . " WHERE entrykey='" . $aNum["entrykey"] . "'";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute();
  }

  // Read all team alias records for event
  public function readEventAliasTable($eventCode)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "SELECT 
        entrykey,
        eventcode,
        teamnumber,
        aliasnumber
        FROM " . $dbConfig["aliastable"] .
      " WHERE eventcode='" . $eventCode . "'";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute();
    $result = $prepared_statement->fetchAll();
    return $result;
  }

  ////////////////////////////////////////////////////////
  ////////////////////// Watch List //////////////////////
  ////////////////////////////////////////////////////////

  // Write team watch status record into table and replace if an entry already exists
  public function writeWatchStatusToTable($wStat)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "REPLACE INTO " . $dbConfig["watchtable"] .
      "(
        entrykey,
        eventcode,
        teamnumber,
        status
      )
      VALUES
      (
        :entrykey,
        :eventcode,
        :teamnumber,
        :status
      )";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute($wStat);
  }

  // Delete team watch status record from table
  public function deleteWatchStatusFromTable($wStat)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "DELETE FROM " . $dbConfig["watchtable"] . " WHERE entrykey='" . $wStat["entrykey"] . "'";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute();
  }

  // Read all team watch status records for event
  public function readEventWatchList($eventCode)
  {
    $dbConfig = $this->readDbConfig();
    $sql = "SELECT 
        entrykey,
        eventcode,
        teamnumber,
        status
        FROM " . $dbConfig["watchtable"] .
      " WHERE eventcode='" . $eventCode . "'";
    $prepared_statement = $this->conn->prepare($sql);
    $prepared_statement->execute();
    $result = $prepared_statement->fetchAll();
    return $result;
  }

  ////////////////////////////////////////////////////////
  ////////////////////// Database Creation ///////////////
  ////////////////////////////////////////////////////////

  // Create the database
  public function createDB()
  {
    error_log("createDB in dbHandler");
    $dbConfig = $this->readDbConfig();
    $this->conn = $this->connectToServer();
    $statement = $this->conn->prepare('CREATE DATABASE IF NOT EXISTS ' . $dbConfig["db"]);
    if (!$statement->execute())
    {
      throw new Exception("createDB Error: CREATE DATABASE query failed.");
    }
  }

  ////////////////////////////////////////////////////////
  ////////////////////// Table Creation //////////////////
  ////////////////////////////////////////////////////////

  // Create Match Data Table 
  public function createMatchTable()
  {
    error_log("Creating Match Table");
    $conn = $this->connectToDB();
    $dbConfig = $this->readDbConfig();
    $query = "CREATE TABLE " . $dbConfig["db"] . "." . $dbConfig["datatable"] .
      " (
        entrykey VARCHAR(60) NOT NULL PRIMARY KEY,
        eventcode VARCHAR(10) NOT NULL,
        matchnumber VARCHAR(10) NOT NULL,
        teamnumber VARCHAR(10) NOT NULL,
        teamalias VARCHAR(10) NOT NULL,
        scoutname VARCHAR(30) NOT NULL,
        died TINYINT UNSIGNED NOT NULL,
        autonShootPreload TINYINT UNSIGNED NOT NULL,
        autonPreloadAccuracy TINYINT UNSIGNED NOT NULL,
        autonHoppersShot TINYINT UNSIGNED NOT NULL,
        autonHopperAccuracy TINYINT UNSIGNED NOT NULL,
        autonAllianceZone TINYINT UNSIGNED NOT NULL,
        autonDepot TINYINT UNSIGNED NOT NULL,
        autonOutpost TINYINT UNSIGNED NOT NULL,
        autonNeutralZone TINYINT UNSIGNED NOT NULL,
        autonClimb TINYINT UNSIGNED NOT NULL,
        teleopHoppersUsed TINYINT UNSIGNED NOT NULL,
        teleopHopperAccuracy TINYINT UNSIGNED NOT NULL,
        teleopIntakeAndShoot TINYINT UNSIGNED NOT NULL,
        teleopNeutralToAlliance TINYINT UNSIGNED NOT NULL,
        teleopAllianceToAlliance TINYINT UNSIGNED NOT NULL,
        teleopPassingRate TINYINT UNSIGNED NOT NULL,
        teleopDefenseLevel TINYINT UNSIGNED NOT NULL,
        endgameCageClimb TINYINT UNSIGNED NOT NULL,
        endgameStartClimb TINYINT UNSIGNED NOT NULL,
        comment VARCHAR(500) NOT NULL,
        INDEX (eventcode, matchnumber, teamnumber)
      )";
    $statement = $conn->prepare($query);
    if (!$statement->execute())
    {
      throw new Exception("createTables Error: CREATE TABLE " . $dbConfig["datatable"] . " query failed.");
    }
  }

  // Create TBA Response CacheTable
  public function createTBATable()
  {
    error_log("Creating TBA Table");
    $conn = $this->connectToDB();
    $dbConfig = $this->readDbConfig();
    $query = "CREATE TABLE " . $dbConfig["db"] . "." . $dbConfig["tbatable"] .
      " (
        requestURI VARCHAR(100) NOT NULL PRIMARY KEY,
        expiryTime BIGINT NOT NULL,
        response MEDIUMTEXT NOT NULL
      )";
    $statement = $conn->prepare($query);
    if (!$statement->execute())
    {
      throw new Exception("createTBATable Error: CREATE TABLE " . $dbConfig["tbatable"] . " query failed.");
    }
  }

  // Create Pit Data Table
  public function createPitTable()
  {
    error_log("Creating Pit Table");
    $conn = $this->connectToDB();
    $dbConfig = $this->readDbConfig();
    $query = "CREATE TABLE " . $dbConfig["db"] . "." . $dbConfig["pittable"] .
      " (
        entrykey VARCHAR(60) NOT NULL PRIMARY KEY,
        eventcode VARCHAR(10) NOT NULL,
        teamnumber VARCHAR(10) NOT NULL,
        scoutname VARCHAR(30) NOT NULL,
        swerve TINYINT UNSIGNED NOT NULL,
        drivemotors VARCHAR(20) NOT NULL,
        spareparts TINYINT UNSIGNED NOT NULL,
        proglanguage VARCHAR(20) NOT NULL,
        computervision TINYINT UNSIGNED NOT NULL,
        pitorg TINYINT UNSIGNED NOT NULL,
        preparedness TINYINT UNSIGNED NOT NULL,
        numbatteries VARCHAR(8) NOT NULL,
        caphopper VARCHAR(8) NOT NULL,
        trenchdrive TINYINT UNSIGNED NOT NULL,
        climbable TINYINT UNSIGNED NOT NULL,
        climblevel TINYINT UNSIGNED NOT NULL,

        INDEX (eventcode)
      )";
    $statement = $conn->prepare($query);
    if (!$statement->execute())
    {
      throw new Exception("createPitTable Error: CREATE TABLE " . $dbConfig["pittable"] . " query failed.");
    }
  }

  // Create Strategic Data Table
  public function createStrategicTable()
  {
    error_log("Creating Strategic Table");
    $conn = $this->connectToDB();
    $dbConfig = $this->readDbConfig();
    $query = "CREATE TABLE " . $dbConfig["db"] . "." . $dbConfig["strategictable"] .
      " (
        entrykey VARCHAR(60) NOT NULL PRIMARY KEY,
        eventcode VARCHAR(10) NOT NULL,
        matchnumber VARCHAR(10) NOT NULL,
        teamnumber VARCHAR(10) NOT NULL,
        scoutname VARCHAR(30) NOT NULL,
        activeShiftLoadedHopper TINYINT UNSIGNED NOT NULL,
        activeShiftShotHopper TINYINT UNSIGNED NOT NULL,
        activeShiftPassingFromAlliance TINYINT UNSIGNED NOT NULL,
        activeShiftPassingFromNeutral TINYINT UNSIGNED NOT NULL,
        activeShiftDefenseAgainstShooter TINYINT UNSIGNED NOT NULL,
        activeShiftDefenseAtBump TINYINT UNSIGNED NOT NULL,
        activeShiftDefenseAtTrench TINYINT UNSIGNED NOT NULL,
        inactiveShiftLoadedHopper TINYINT UNSIGNED NOT NULL,
        inactiveShiftShotHopper TINYINT UNSIGNED NOT NULL,
        inactiveShiftPassingFromAlliance TINYINT UNSIGNED NOT NULL,
        inactiveShiftPassingFromNeutral TINYINT UNSIGNED NOT NULL,
        inactiveShiftDefenseAgainstShooter TINYINT UNSIGNED NOT NULL,
        inactiveShiftDefenseAtBump TINYINT UNSIGNED NOT NULL,
        inactiveShiftDefenseAtTrench TINYINT UNSIGNED NOT NULL,
        againstDefenseEffectiveness TINYINT UNSIGNED NOT NULL,
        bumpTippedOver TINYINT UNSIGNED NOT NULL,
        bumpBottomedOut TINYINT UNSIGNED NOT NULL,
        bumpAvoidedDefender TINYINT UNSIGNED NOT NULL,
        bumpGotStuckOnFuel TINYINT UNSIGNED NOT NULL,
        fouls TINYINT UNSIGNED NOT NULL,
        problem_comment VARCHAR(500) NOT NULL,
        general_comment VARCHAR(500) NOT NULL,
        INDEX (eventcode)
      )";
    $statement = $conn->prepare($query);
    if (!$statement->execute())
    {
      throw new Exception("createStrategicTable Error: CREATE TABLE " . $dbConfig["strategictable"] . " query failed.");
    }
  }

  // Create Scout Name Table
  public function createScoutTable()
  {
    error_log("Creating Scout Table");
    $conn = $this->connectToDB();
    $dbConfig = $this->readDbConfig();
    $query = "CREATE TABLE " . $dbConfig["db"] . "." . $dbConfig["scouttable"] .
      " (
        entrykey VARCHAR(60) NOT NULL PRIMARY KEY,
        eventcode VARCHAR(10) NOT NULL,
        scoutname VARCHAR(30) NOT NULL,
        INDEX (eventcode)
      )";
    $statement = $conn->prepare($query);
    if (!$statement->execute())
    {
      throw new Exception("createScoutTable Error: CREATE TABLE " . $dbConfig["scouttable"] . " query failed.");
    }
  }

  // Write JSON data to file
  public function writeJSONToFile($dat, $name)
  {
    // Write ini file string to actual file
    if ($fp = fopen($name, 'w'))
    {
      $startTime = microtime(True);
      do
      {
        $writeLock = flock($fp, LOCK_EX);
        if (!$writeLock)
        {
          usleep(round(21350));
        }
      } while ((!$writeLock) and ((microtime(True) - $startTime) < 5));

      if ($writeLock)
      {
        fwrite($fp, $dat);
        flock($fp, LOCK_UN);
      }
    }
    fclose($fp);
  }

  // Create Hopper Cap Data Table
  public function createHopperCapTable()
  {
    error_log("Creating HopperCapTable");
    $conn = $this->connectToDB();
    $dbConfig = $this->readDbConfig();
    $query = "CREATE TABLE " . $dbConfig["db"] . "." . $dbConfig["hoppercaptable"] .
      " (
        entrykey VARCHAR(60) NOT NULL PRIMARY KEY,
        eventcode VARCHAR(10) NOT NULL,
        teamnumber VARCHAR(10) NOT NULL,
        hoppercap TINYINT UNSIGNED NOT NULL,
        INDEX (eventcode)
      )";
    $statement = $conn->prepare($query);
    if (!$statement->execute())
    {
      throw new Exception("createHopperCapTable Error: CREATE TABLE " . $dbConfig["hoppercaptable"] . " query failed.");
    }
  }

  // Create Team Alias Data Table
  public function createAliasTable()
  {
    error_log("Creating Alias Table");
    $conn = $this->connectToDB();
    $dbConfig = $this->readDbConfig();
    $query = "CREATE TABLE " . $dbConfig["db"] . "." . $dbConfig["aliastable"] .
      " (
        entrykey VARCHAR(60) NOT NULL PRIMARY KEY,
        eventcode VARCHAR(10) NOT NULL,
        teamnumber VARCHAR(10) NOT NULL,
        aliasnumber VARCHAR(10) NOT NULL,
        INDEX (eventcode)
      )";
    $statement = $conn->prepare($query);
    if (!$statement->execute())
    {
      throw new Exception("createAliasTable Error: CREATE TABLE " . $dbConfig["aliastable"] . " query failed.");
    }
  }

  // Create Team Watch Data Table
  public function createWatchTable()
  {
    error_log("Creating Watch Table");
    $conn = $this->connectToDB();
    $dbConfig = $this->readDbConfig();
    $query = "CREATE TABLE " . $dbConfig["db"] . "." . $dbConfig["watchtable"] .
      " (
        entrykey VARCHAR(60) NOT NULL PRIMARY KEY,
        eventcode VARCHAR(10) NOT NULL,
        teamnumber VARCHAR(10) NOT NULL,
        status VARCHAR(10) NOT NULL,
        INDEX (eventcode)
      )";
    $statement = $conn->prepare($query);
    if (!$statement->execute())
    {
      throw new Exception("createWatchTable Error: CREATE TABLE " . $dbConfig["watchtable"] . " query failed.");
    }
  }

  ////////////////////////////////////////////////////////
  ////////////////////// Database Config /////////////////
  ////////////////////////////////////////////////////////

  // Read and return the database configutration file
  public function readDbConfig()
  {
    // If File doesn't exist, instantiate array as empty
    if (!file_exists($this->dbIniFile))
    {
      error_log("dbHandler: readDbConfig: db_config file does NOT exist!");
      $ini_arr = array();
    }
    else
    {
      try
      {
        error_log("dbHandler: readDbConfig: reading db_config file");
        $ini_arr = parse_ini_file($this->dbIniFile);
      }
      catch (Exception $e)
      {
        error_log("dbHandler: can't read existing db_config file, so  creating a new one");
        $ini_arr = array();
      }
    }

    // If required keys don't exist, instantiate them to default empty string
    foreach ($this->configKeys as $key)
    {
      if (!isset($ini_arr[$key]))
      {
        $ini_arr[$key] = "";
      }

      # Specific checking for match filters
      if ($key === "useP" || $key === "useQm" || $key === "useSf" || $key === "useF")
      {
        $ini_arr[$key] = ($ini_arr[$key] === "" || $ini_arr[$key] === "1" || $ini_arr[$key] === "true");
      }
    }
    return $ini_arr;
  }

  // Write database configuration file
  public function writeDbConfig($dat)
  {
    // Get values to write
    // If value is not in input, read from current DB config
    $currDBConfig = $this->readDbConfig();
    foreach ($dat as $key => $value)
    {
      error_log("dbHandler: writeDbConfig: setting currDBConfig[$key] to $value");
      $currDBConfig[$key] = $value;
    }

    // Build ini file string
    $cfgData = "";
    foreach ($currDBConfig as $key => $value)
    {
      $cfgData = $cfgData . $key . "=" . $value . "\r\n";
    }

    // Write ini file string to actual file
    if ($fp = fopen($this->dbIniFile, 'w'))
    {
      $startTime = microtime(True);
      do
      {
        $writeLock = flock($fp, LOCK_EX);
        if (!$writeLock)
        {
          usleep(round(21350));
        }
      } while ((!$writeLock) and ((microtime(True) - $startTime) < 5));

      if ($writeLock)
      {
        fwrite($fp, $cfgData);
        flock($fp, LOCK_UN);
      }
    }
    fclose($fp);
  }

  // Check database status by connecting to server, database, and each table
  public function getDBStatus(): array
  {
    $dbConfig = $this->readDbConfig();
    $out = array();
    //
    $dbStatus["server"] = $dbConfig["server"];
    $dbStatus["db"] = $dbConfig["db"];
    $dbStatus["tbakey"] = $dbConfig["tbakey"];
    $dbStatus["eventcode"] = $dbConfig["eventcode"];
    $dbStatus["username"] = $dbConfig["username"];
    $dbStatus["dbExists"] = false;
    $dbStatus["serverExists"] = false;
    $dbStatus["matchTableExists"] = false;
    $dbStatus["tbaTableExists"] = false;
    $dbStatus["pitTableExists"] = false;
    $dbStatus["strategicTableExists"] = false;
    $dbStatus["scoutTableExists"] = false;
    $dbStatus["aliasTableExists"] = false;
    $dbStatus["watchTableExists"] = false;
    $dbStatus["hopperCapTableExists"] = false;
    $dbStatus["useP"] = $dbConfig["useP"];
    $dbStatus["useQm"] = $dbConfig["useQm"];
    $dbStatus["useSf"] = $dbConfig["useSf"];
    $dbStatus["useF"] = $dbConfig["useF"];

    // Server Connection
    if ($dbConfig["server"] != "")
    {
      try
      {
        $dsn = "mysql:host=" . $dbConfig["server"] . ";charset=" . $this->charset;
        $conn = new PDO($dsn, $dbConfig["username"], $dbConfig["password"]);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbStatus["serverExists"] = true;
      }
      catch (PDOException $e)
      {
        error_log("dbHandler: getDBStatus: server connection failed! - " . $e->getMessage());
      }

      // DB Connection
      if ($dbStatus["serverExists"] == true)
      {
        try
        {
          $dsn = "mysql:host=" . $dbConfig["server"] . ";dbname=" . $dbConfig["db"] . ";charset=" . $this->charset;
          $conn = new PDO($dsn, $dbConfig["username"], $dbConfig["password"]);
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $val = $conn->query('SHOW DATABASES LIKE "' . $dbConfig["db"] . '"');
          if ($val->rowCount() != 0)
          {
            $dbStatus["dbExists"] = true;
          }
          else
          {
            error_log("dbHandler: getDBStatus: database does not exist");
          }
        }
        catch (PDOException $e)
        {
          error_log("dbHandler: getDBStatus: database connection failed! - " . $e->getMessage());
        }

        // Match data able Connection
        if ($dbStatus["dbExists"] == true)
        {
          try
          {
            $dsn = "mysql:host=" . $dbConfig["server"] . ";dbname=" . $dbConfig["db"] . ";charset=" . $this->charset;
            $conn = new PDO($dsn, $dbConfig["username"], $dbConfig["password"]);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $val = $conn->query('SELECT * FROM ' . $dbConfig["datatable"]);
            $dbStatus["matchTableExists"] = true;
          }
          catch (PDOException $e)
          {
            error_log("dbHandler: getDBStatus: match data table missing! " . $e->getMessage());
          }

          // Pit table Connection
          try
          {
            $dsn = "mysql:host=" . $dbConfig["server"] . ";dbname=" . $dbConfig["db"] . ";charset=" . $this->charset;
            $conn = new PDO($dsn, $dbConfig["username"], $dbConfig["password"]);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $val = $conn->query('SELECT * FROM ' . $dbConfig["pittable"]);
            $dbStatus["pitTableExists"] = true;
          }
          catch (PDOException $e)
          {
            error_log("dbHandler: getDBStatus: pit data table missing! - " . $e->getMessage());
          }

          // Strategic table Connection
          try
          {
            $dsn = "mysql:host=" . $dbConfig["server"] . ";dbname=" . $dbConfig["db"] . ";charset=" . $this->charset;
            $conn = new PDO($dsn, $dbConfig["username"], $dbConfig["password"]);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $val = $conn->query('SELECT * FROM ' . $dbConfig["strategictable"]);
            $dbStatus["strategicTableExists"] = true;
          }
          catch (PDOException $e)
          {
            error_log("dbHandler: getDBStatus: strategic data table missing! - " . $e->getMessage());
          }

          // TBA table Connection
          try
          {
            $dsn = "mysql:host=" . $dbConfig["server"] . ";dbname=" . $dbConfig["db"] . ";charset=" . $this->charset;
            $conn = new PDO($dsn, $dbConfig["username"], $dbConfig["password"]);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $val = $conn->query('SELECT * FROM ' . $dbConfig["tbatable"]);
            $dbStatus["tbaTableExists"] = true;
          }
          catch (PDOException $e)
          {
            error_log("dbHandler: getDBStatus: tba table missing! - " . $e->getMessage());
          }

          // Scout table Connection
          try
          {
            $dsn = "mysql:host=" . $dbConfig["server"] . ";dbname=" . $dbConfig["db"] . ";charset=" . $this->charset;
            $conn = new PDO($dsn, $dbConfig["username"], $dbConfig["password"]);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $val = $conn->query('SELECT * FROM ' . $dbConfig["scouttable"]);
            $dbStatus["scoutTableExists"] = true;
          }
          catch (PDOException $e)
          {
            error_log("dbHandler: getDBStatus: scout table missing! - " . $e->getMessage());
          }

          // Alias table Connection
          try
          {
            $dsn = "mysql:host=" . $dbConfig["server"] . ";dbname=" . $dbConfig["db"] . ";charset=" . $this->charset;
            $conn = new PDO($dsn, $dbConfig["username"], $dbConfig["password"]);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $val = $conn->query('SELECT * FROM ' . $dbConfig["aliastable"]);
            $dbStatus["aliasTableExists"] = true;
          }
          catch (PDOException $e)
          {
            error_log("dbHandler: getDBStatus: alias table missing! - " . $e->getMessage());
          }

          // HopperCap table Connection
          try
          {
            $dsn = "mysql:host=" . $dbConfig["server"] . ";dbname=" . $dbConfig["db"] . ";charset=" . $this->charset;
            $conn = new PDO($dsn, $dbConfig["username"], $dbConfig["password"]);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $val = $conn->query('SELECT * FROM ' . $dbConfig["hoppercaptable"]);
            $dbStatus["hopperCapTableExists"] = true;
          }
          catch (PDOException $e)
          {
            error_log("dbHandler: getDBStatus: hopperCap table missing! - " . $e->getMessage());
          }

          // Watch table Connection
          try
          {
            $dsn = "mysql:host=" . $dbConfig["server"] . ";dbname=" . $dbConfig["db"] . ";charset=" . $this->charset;
            $conn = new PDO($dsn, $dbConfig["username"], $dbConfig["password"]);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $val = $conn->query('SELECT * FROM ' . $dbConfig["watchtable"]);
            $dbStatus["watchTableExists"] = true;
          }
          catch (PDOException $e)
          {
            error_log("dbHandler: getDBStatus: watch table missing! - " . $e->getMessage());
          }
        }
      }
    }
    return $dbStatus;
  }
}

?>

