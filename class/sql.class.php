<?php

class Sql extends connection {

    function query($name, $filter) {

        $sql = '';
        switch ($name) {
            case "TrainingSet":
                $sql = "SELECT(SELECT COUNT(*) FROM TrainingSet WHERE idCodiceLingua = 1 
                AND TipoSet = 'I'
                AND TipoRiga = 'W'
                ) AS Totale,
                (
                SELECT
                  COUNT(*)
                FROM
                  TrainingSet
                WHERE idCodiceLingua = 1 
                  AND TipoSet = 'I'
                  AND TipoRiga = 'W'
                  AND Peso = -1
                ) AS Negativi,
                (
                SELECT
                  COUNT(*)
                FROM
                  TrainingSet
                WHERE idCodiceLingua = 1 
                  AND TipoSet = 'I'
                  AND TipoRiga = 'W'
                  AND Peso = +1
                ) AS Positivi" . $filter;
                break;


            case "2"://2. Funzione Dashboard

                $sql = "SELECT idRicerca,Keyword AS title, 'false' AS allday, CONCAT('Utente: ',IF(ISNULL(utente), 'xxxxx',utente),
                    '<br>Stato: ',IF(ISNULL(Stato), '',Stato),
                    '<br>Numero Tweet: ',IF(ISNULL(NumeroTweet), '',NumeroTweet),
                    '<br>Tweet Analizzati:',IF(ISNULL(TweetAnalizzati), '',TweetAnalizzati),
                    '<br>Max Tweet:',IF(ISNULL(MaxTweet), '',MaxTweet), 
                    '<br>Ricerca Terminata:',IF(ISNULL(RicercaTerminata), '',RicercaTerminata),
                    '<br>Lingua: ',IF(ISNULL(ListaCodiceLingua), '',ListaCodiceLingua),
                    '<br>Inizio elaborazione: ',IF(ISNULL(InizioElaborazione), '',InizioElaborazione),
                    '<br>Fine elaborazione: ',IF(ISNULL(FineElaborazione), '',FineElaborazione),
                    '<br>Data e ora schedulazione: ',IF(ISNULL(DataOraSchedulazione), '',DataOraSchedulazione)
                  ) 
                    AS description, '#' AS url, IF(ISNULL(DataOraSchedulazione), DataOraRichiesta,DataOraSchedulazione) AS start 
                    FROM richieste " . $filter . "  ";
                break;

            case "2.1"://2. Funzione Lista
                $sql = "SELECT * FROM Richieste " . $filter . "  ";
                break;
            case "6.2.1"://6.2.1. Dati per la testat
                $sql = "SELECT ScreenName,NAME,DataRegistrazioneUser,
                    StatusesCount,FollowersCount,FriendsCount,
                    Description,location,PlaceName,
                    PlaceFullName,PlaceCountry,ProfileImageURLHTTPS,
                    ProfileBannerURL,FavouritesCount,ListedCount,
                    TweetTimeStampStringa
                    FROM AnalisiTweets
                    WHERE ScreenName = '000120o'
                    ORDER BY TweetTimeStampStringa DESC LIMIT 1";
                break;
            case "6.2.2":
                $sql = "SELECT DISTINCT Keyword FROM AnalisiTweets WHERE ScreenName = '000120o' ORDER BY Keyword " . $filter;
                break;
            case "7.1":
                $sql = "SELECT
                    (SELECT COUNT(*) FROM TrainingSetIntegrativo WHERE CodiceLingua = 'it') AS Totale,
                    (SELECT COUNT(*) FROM TrainingSetIntegrativo WHERE CodiceLingua = 'it' AND Peso = -1) AS Negativi,
                    (SELECT COUNT(*) FROM TrainingSetIntegrativo WHERE CodiceLingua = 'it' AND Peso = 0) AS Neutri,
                    (SELECT COUNT(*) FROM TrainingSetIntegrativo WHERE CodiceLingua = 'it' AND Peso = +1) AS Positivi " . $filter;
                break;


            case "8.1.2":
                $sql = "SELECT Word, SUM(WordCount) AS Conteggio 
                FROM AnalisiTweetsWordCount WHERE  Keyword in ('key1', 'Key2', 'Keyn')
                AND TweetTimeStampStringa BETWEEN '0000' AND '9999'
                AND CodiceLingua IN('l1','l2') AND (AT = true) GROUP by Word ORDER by Conteggio DESC LIMIT 50 " . $filter;
                break;

            case "8.1.3":
                $sql = "SELECT Word, SUM(WordCount) AS Conteggio FROM AnalisiTweetsWordCount 
                WHERE  Keyword in ('key1', 'Key2', 'Keyn') AND TweetTimeStampStringa BETWEEN '0000' AND '9999'
                AND CodiceLingua IN('l1','l2') AND (Hashtag = true) GROUP by Word ORDER by Conteggio DESC LIMIT 50 " . $filter;
                break;

            case "8.1.4":
                $sql = "SELECT Word, SUM(WordCount) AS Conteggio 
                    FROM AnalisiTweetsWordCount WHERE  IdRicerca = '20170621113103'
                    AND CodiceLingua IN('l1','l2') AND (AT = FALSE) AND (Hashtag = FALSE)  AND (URL = FALSE) 
                    AND Word NOT IN(SELECT StopWord FROM StopWords) 
                    GROUP by Word ORDER by Conteggio DESC LIMIT 50 " . $filter;
                break;

            case "8.1.5":
                $sql = "SELECT Word, sum(WordCount) AS Conteggio 
                FROM AnalisiTweetsWordCount 
                WHERE  IdRicerca = '20170621113103' AND CodiceLingua IN('l1','l2') AND (Hashtag = true) GROUP by Word
                ORDER by Conteggio DESC LIMIT 50 " . $filter;
                break;

            case "8.1.6":
                $sql = "SELECT Word, SUM(WordCount) AS Conteggio 
                FROM AnalisiTweetsWordCount 
                WHERE  IdRicerca = '20170621113103'
                AND CodiceLingua IN('l1','l2')
                AND (At = true) 
                GROUP by Word
                ORDER by Conteggio DESC
                LIMIT 50 " . $filter;
                break;

            case "8.3.1":
                $sql = "SELECT '1',ScreenName,Word,SUM(WordCount) AS Conteggio FROM AnalisiTweetsWordCount
                WHERE ScreenName IS NOT NULL 
                AND Keyword IN('minniti') AND TweetTimeStampStringa BETWEEN '0000' AND '9999'
                AND CodiceLingua IN('l1','l2') AND(AT = FALSE) AND(Hashtag = FALSE) AND(URL = FALSE) 
                AND Word NOT IN(
                SELECT StopWord FROM StopWords)
                GROUP BY
                ScreenName,
                Word
                HAVING
                (SUM(WordCount) > 3)
                UNION ALL
                SELECT
                '2',
                Word,
                ScreenName,
                SUM(WordCount) AS Conteggio
                FROM
                AnalisiTweetsWordCount
                WHERE
                ScreenName IS NOT NULL 
                AND Keyword IN('minniti') 
                AND TweetTimeStampStringa BETWEEN '0000' AND '9999'
                AND CodiceLingua IN('l1','l2')
                AND(AT = FALSE) 
                AND(Hashtag = FALSE) 
                AND(URL = FALSE) 
                AND Word NOT IN(SELECT StopWord FROM StopWords)
                GROUP BY
                Word,
                ScreenName
                HAVING
                (SUM(WordCount) > 3) " . $filter;
                break;

            case "8.3.2. Query Screname (per Keyword & periodo opzionale)":
                $sql = "SELECT
                '1',
                ScreenName,
                Word,
                SUM(WordCount) AS Conteggio
                FROM
                AnalisiTweetsWordCount
                WHERE
                ScreenName IS NOT NULL 
                AND Keyword IN('minniti') 
                AND TweetTimeStampStringa BETWEEN '0000' AND '9999'
                AND CodiceLingua IN('l1','l2')
                AND(AT = TRUE) 
                GROUP BY
                ScreenName,
                Word
                HAVING
                (SUM(WordCount) > 3)
                UNION ALL
                SELECT
                '2',
                Word,
                ScreenName,
                SUM(WordCount) AS Conteggio
                FROM
                AnalisiTweetsWordCount
                WHERE
                ScreenName IS NOT NULL 
                AND Keyword IN('minniti') 
                AND TweetTimeStampStringa BETWEEN '0000' AND '9999'
                AND CodiceLingua IN('l1','l2')
                AND(AT = TRUE) 
                GROUP BY
                Word,
                ScreenName
                HAVING
                (SUM(WordCount) > 3) " . $filter;
                break;

            case "8.3.3. Query Hashtag (per Keyword & periodo opzionale)":
                $sql = "SELECT
                '1',
                ScreenName,
                Word,
                SUM(WordCount) AS Conteggio
                FROM
                AnalisiTweetsWordCount
                WHERE
                ScreenName IS NOT NULL 
                AND Keyword IN('minniti') 
                AND TweetTimeStampStringa BETWEEN '0000' AND '9999'
                AND CodiceLingua IN('l1','l2')
                AND(Hashtag = TRUE) 
                GROUP BY
                ScreenName,
                Word
                HAVING
                (SUM(WordCount) > 3)
                UNION ALL
                SELECT
                '2',
                Word,
                ScreenName,
                SUM(WordCount) AS Conteggio
                FROM
                AnalisiTweetsWordCount
                WHERE
                ScreenName IS NOT NULL 
                AND Keyword IN('minniti') 
                AND TweetTimeStampStringa BETWEEN '0000' AND '9999'
                AND CodiceLingua IN('l1','l2')
                AND(Hashtag = TRUE) 
                GROUP BY
                Word,
                ScreenName
                HAVING
                (SUM(WordCount) > 3) " . $filter;
                break;


            case "8.3.4. Query Word (per idRicerca)":
                $sql = "SELECT
  '1',
  ScreenName,
  Word,
  SUM(WordCount) AS Conteggio
FROM
  AnalisiTweetsWordCount
WHERE
  ScreenName IS NOT NULL 
AND IdRicerca = '20170621113103'
AND CodiceLingua IN('l1','l2') 
AND(AT = FALSE) 
AND(Hashtag = FALSE) 
AND(URL = FALSE) 
AND Word NOT IN(
  SELECT
    StopWord
  FROM
    StopWords
)
GROUP BY
  ScreenName,
  Word
HAVING
  (SUM(WordCount) > 3)
UNION ALL
SELECT
  '2',
  Word,
  ScreenName,
  SUM(WordCount) AS Conteggio
FROM
  AnalisiTweetsWordCount
WHERE
  ScreenName IS NOT NULL 
AND IdRicerca = '20170621113103'
AND CodiceLingua IN('l1','l2') 
AND(AT = FALSE) 
AND(Hashtag = FALSE) 
AND(URL = FALSE) 
AND Word NOT IN(
  SELECT
    StopWord
  FROM
    StopWords
)
GROUP BY
  Word,
  ScreenName
HAVING
  (SUM(WordCount) > 3) " . $filter;
                break;


            case "8.3.5. Query Screname (per idRicerca)":
                $sql = "SELECT
  '1',
  ScreenName,
  Word,
  SUM(WordCount) AS Conteggio
FROM
  AnalisiTweetsWordCount
WHERE
  ScreenName IS NOT NULL 
AND IdRicerca = '20170621113103'
AND CodiceLingua IN('l1','l2') 
AND(AT = TRUE) 
GROUP BY
  ScreenName,
  Word
HAVING
  (SUM(WordCount) > 3)
UNION ALL
SELECT
  '2',
  Word,
  ScreenName,
  SUM(WordCount) AS Conteggio
FROM
  AnalisiTweetsWordCount
WHERE
  ScreenName IS NOT NULL 
AND IdRicerca = '20170621113103'
AND CodiceLingua IN('l1','l2') 
AND(AT = TRUE) 
GROUP BY
  Word,
  ScreenName
HAVING
  (SUM(WordCount) > 3) " . $filter;
                break;


            case "8.3.6. Query Hashtag (per idRicerca)":
                $sql = "SELECT
  '1',
  ScreenName,
  Word,
  SUM(WordCount) AS Conteggio
FROM
  AnalisiTweetsWordCount
WHERE
  ScreenName IS NOT NULL 
AND IdRicerca = '20170621113103'
AND CodiceLingua IN('l1','l2') 
AND(Hashtag = TRUE) 
GROUP BY
  ScreenName,
  Word
HAVING
  (SUM(WordCount) > 3)
UNION ALL
SELECT
  '2',
  Word,
  ScreenName,
  SUM(WordCount) AS Conteggio
FROM
  AnalisiTweetsWordCount
WHERE
  ScreenName IS NOT NULL 
AND IdRicerca = '20170621113103'
AND CodiceLingua IN('l1','l2') 
AND(Hashtag = TRUE) 
GROUP BY
  Word,
  ScreenName
HAVING
  (SUM(WordCount) > 3) " . $filter;
                break;

            case "8.4.1.1. Per Mese":
                $sql = "SELECT
  LEFT(CAST(TweetTimeStampStringa AS CHAR), 7) as Periodo,
  AVG(Confidence) AS Totale
FROM
  AnalisiTweets
WHERE Keyword IN('minniti','immigrazione', 'migranti') 
  AND Lingua IN('it')
GROUP BY
  LEFT(
    CAST(TweetTimeStampStringa AS CHAR), 7)
ORDER BY
  LEFT(CAST(TweetTimeStampStringa AS CHAR), 7) " . $filter;
                break;

            case "8.4.1.2. Per Quindicina":
                $sql = "Select Periodo, AVG(Confidence)
FROM(
SELECT
  CONCAT(LEFT(CAST(TweetTimeStampStringa AS CHAR), 7), ' (01/15)') as Periodo,
  if(SUBSTRING(CAST(TweetTimeStampStringa AS CHAR), 9, 2) <16,Confidence, 0) AS Confidence
FROM
  AnalisiTweets
WHERE Keyword IN('minniti','immigrazione', 'migranti') 
  AND Lingua IN('it')
  UNION all
SELECT
  CONCAT(LEFT(CAST(TweetTimeStampStringa AS CHAR), 7), ' (16/30)') as Periodo,
  if(SUBSTRING(CAST(TweetTimeStampStringa AS CHAR), 9, 2) >15,Confidence, 0) AS Confidence
FROM
  AnalisiTweets
WHERE Keyword IN('minniti','immigrazione', 'migranti') 
  AND Lingua IN('it')
) as TS
Group by Periodo
Order By Periodo " . $filter;
                break;

            case "8.4.1.3. Per Giorno":
                $sql = "SELECT
  LEFT(CAST(TweetTimeStampStringa AS CHAR), 10) as Periodo,
  AVG(Confidence) AS Totale
FROM
  AnalisiTweets
WHERE Keyword IN('minniti','immigrazione', 'migranti') 
  AND Lingua IN('it')
GROUP BY
  LEFT(
    CAST(TweetTimeStampStringa AS CHAR), 10)
ORDER BY
  LEFT(CAST(TweetTimeStampStringa AS CHAR), 10) " . $filter;
                break;

            case "8.5.1. Query":
                $sql = "SELECT Confidence,COUNT(*) AS Conteggio FROM AnalisiTweets WHERE Keyword IN('minniti') AND Lingua IN('it') 
                        AND TweetTimeStampStringa BETWEEN '0000' AND '9999' GROUP BY Confidence ORDER BY Confidence

SELECT Confidence, COUNT(*) AS Conteggio FROM AnalisiTweets
WHERE
  IdRicerca = '20170621113103' 
  AND Lingua IN('it') 
GROUP BY
  Confidence
ORDER BY
  Confidence " . $filter;
                break;
            case "tipo_keyword":
                $sql = "SELECT Keyword FROM AnalisiTweetsWordCount " . $filter;
                break;
            case "tipo_lingua":
                $sql = "SELECT * FROM Lingue " . $filter;
                break;
            default:
                $sql = $filter;
                break;
        }

        return $sql;
    }

    public function SelectCount($sql) {

        $result = $this->mysqli->query($sql);
        if (!$result) {
            throw new Exception("Database Error [{$this->database->errno}] {$this->database->error}");
            $total = 0;
        } else {
            $row = $result->fetch_assoc();
            $total = $row['Total'];

            $result->close();
        }
        return $total;
    }

    public function SelectArray($sql) {
        // echo $sql;
        // $this->mysqli->query("SET NAMES 'utf8'");
        $result = $this->mysqli->query($sql);
//print_r($result);
        $array = null;
        if ($result->num_rows > 0) {
            while ($CurRec = $result->fetch_assoc()) {
                $array[] = $CurRec;
            }

            /*
              $myArray = array();
              while($row = $result->fetch_assoc())
              {
              $tempArray = $row;
              array_push($myArray, $tempArray);

              //if($row)
              //$array[] =  json_encode($row);


              }
             */
            //$array = $result->fetch_all(MYSQLI_ASSOC);
            //  print_r($array);
            $result->close();
            $result = null;
            //$this->mysqli->close();
        }

        return $array;
    }

    public function SelectView($sql) {
        //echo $sql;
        $result = $this->mysqli->query($sql);
        if (!$result) {
            /* no result. do not try to fetch it. */
            header(sprintf("Location: error404.php?action=problem Function selectView"));
        } else {
            /* result. go ahead and fetch it. */
            $row = $result->fetch_assoc();
            $result->close();
        }


        return $row;
    }

    function RichiesteInsert($utente, $Keyword, $MaxTweet, $ListaCodiceLingua, $DataOraSchedulazione) {
        $idRicerca = date("YmdHis");
//        if (trim($DataOraSchedulazione) <> '') {
//            $Stato = 'Analisi Tweet';
//        } else {
            $Stato = 'Waiting for processing...';
        //}

        $RicercaTerminata = '-1';

        $objReturn[] = null;
        $Keyword = $this->mysqli->real_escape_string($Keyword);
        $MaxTweet = $this->mysqli->real_escape_string($MaxTweet);


        if (isset($DataOraSchedulazione) && $DataOraSchedulazione <> null && trim($DataOraSchedulazione) <> '') {
            $date = explode('/', substr($DataOraSchedulazione, 0, 10));
            $DataOraSchedulazione = $date[2] . '-' . $date[1] . '-' . $date[0] . substr($DataOraSchedulazione, 10, 9);
        } else {
            $DataOraSchedulazione = NULL;
        }


        if ($stmt = $this->mysqli->prepare("INSERT INTO `Richieste` (idRicerca,
                                Keyword,
                                utente,
                                Stato,
                                MaxTweet,
                                RicercaTerminata,
                                ListaCodiceLingua,
                                DataOraSchedulazione) VALUES (?,?,?,?,?,?,?,?)")) {
            $stmt->bind_param('isisssss', $idRicerca, $Keyword, $utente, $Stato, $MaxTweet, $RicercaTerminata, $ListaCodiceLingua, $DataOraSchedulazione);

            // Esegui la query ottenuta.
            if ($stmt->execute() == 1) {
                $objReturn[0] = 'ok';
                $objReturn[1] = mysqli_insert_id($this->mysqli);
                $objReturn[2] = 'Successo!! Inseriemnto avvenuto';
            } else {
                $objReturn[0] = 'ko';
                $objReturn[1] = '';
                $objReturn[2] = 'Problemi tecnici. (' . $this->mysqli->error . ')';
            }

            $stmt->close();
            return $objReturn;
        } else {
            $objReturn[0] = 'ko';
            $objReturn[1] = '';
            $objReturn[2] = 'Problemi tecnici. (' . $this->mysqli->error . ')';
        }
    }

    function RichiesteDelete($id) {
        $objReturn[] = null;
        $sql = "DELETE FROM Richieste WHERE idRicerca = " . $id;

        if ($this->mysqli->query($sql) === TRUE) {
            $objReturn[0] = 'ok';
            $objReturn[1] = '';
            $objReturn[2] = 'Operazione effettuata con successo!';
        } else {

            $objReturn[0] = 'ko';
            $objReturn[1] = '';
            $objReturn[2] = 'Problema tecnico durante l\'eliminazione! (' . $this->mysqli->error . ') ';
        }

        return $objReturn;
    }

    function RetrainingInsert($CodiceLingua, $Type) {

        $objReturn[] = null;
        $CodiceLingua = $this->mysqli->real_escape_string($CodiceLingua);
        $Type = $this->mysqli->real_escape_string($Type);


        if ($stmt = $this->mysqli->prepare("INSERT INTO `RichiesteRiaddestramento` (
                                idCodiceLingua,
                                TipoAddestramento,
                                StatoOperazione,
                                DescrizioneStato
                                ) VALUES (?,?,-1,'Waiting for processing...')")) {
            $stmt->bind_param('is', $CodiceLingua, $Type);

            // Esegui la query ottenuta.
            if ($stmt->execute() == 1) {
                $objReturn[0] = 'ok';
                $objReturn[1] = mysqli_insert_id($this->mysqli);
                $objReturn[2] = 'Success!! New Retraining inserted.';
            } else {
                $objReturn[0] = 'ko';
                $objReturn[1] = '';
                $objReturn[2] = '(' . $this->mysqli->error . ')';
            }

            $stmt->close();
            return $objReturn;
        } else {
            $objReturn[0] = 'ko';
            $objReturn[1] = '';
            $objReturn[2] = '(' . $this->mysqli->error . ')';
        }
    }

}

?>