<?php
    session_start();

    // database connection libraries
    if( is_file('conn/pdo_config.inc.php') ){
        include_once('conn/pdo_config.inc.php');
    }

    // general libraries
    if( is_file('lib/lib.inc.php') ){
        include_once('lib/lib.inc.php');
    }

    // TODO: check login/get UserID
    $uid = 1;

    // diaplay any top messages
    if( isset($_GET['ref']) && strcmp($_GET['ref'], "cashadd") == 0 ){
      $topmessage = '<div class="alert alert-success" role="alert">Account balance topped up successfully.</div>';
    }

    if( isset($_GET['ref']) && strcmp($_GET['ref'], "stockbuy") == 0 ){
      $topmessage = '<div class="alert alert-success" role="alert">Stocks purchased successfully and added to your portfolio.</div>';
    }

    if( isset($_GET['ref']) && strcmp($_GET['ref'], "stocksell") == 0 ){
      $topmessage = '<div class="alert alert-success" role="alert">Stocks sold successfully and your portfolio updated.</div>';
    }

    // retrieve cash balance for this user
    $user_bal = -1;
    try {      
      $dbh = $db_pdo->connect();
      $stmt = $dbh->prepare("SELECT user_balance FROM tbl_user WHERE user_id=:uid");
      $stmt->bindParam(':uid', $uid);
      
      if( $stmt->execute() ) {
        while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
          if(isset($row['user_balance'])) {
            $user_bal = $row['user_balance'];
          }
        }
      } else {
        print "<b>Sorry, we ran into an error (#6543435456543)</b>";
        exit(1);
      }
      $dbh = null;
    } catch (PDOException $e) {
        print "<b>Sorry, we ran into an error (#13251843584654) " . $e->getMessage() . "<br/>";
        $dbh = null;
        exit(1);
    }

    // create held stock table and held stocks dropdown (for Sell Stock popup)
    $stocks_held      = '<table class="table table-hover"><thead><tr><th> # </th> <th>Stock</th> <th>Amount held</th> </tr></thead><tbody>';
    $stocks_held_dd   = '<select name="sellstockcode" id="sellstockcode" class="span3" onChange="validateSellPrice()"><option value="">--select a stock to sell</option>';
    
    try {
      // retrieve cash balance for this user
      $dbh = $db_pdo->connect();
      $stmt = $dbh->prepare("SELECT sh_stockcode, sh_amount FROM tbl_stock_holdings WHERE user_id=:uid");
      $stmt->bindParam(':uid', $uid);

      $count = 0;
      if( $stmt->execute() ) {
        while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
          $count++;
          $stockcode = $row['sh_stockcode'];
          $stockamt = $row['sh_amount'];

          $stocks_held .= '<tr> <th scope=row>'.$count.'</th> <td>'.htmlspecialchars($stockcode).'</td> <td>'.htmlspecialchars($stockamt).'</td> </tr>';
          $stocks_held_dd .= '<option value="'.htmlspecialchars($stockcode).'">'.htmlspecialchars($stockcode).'</option>';
        }

        if( $count == 0 ){
          $stocks_held .= '<tr> <td colspan="3"><i>No stocks held currently</td> </tr>';
        }
      } else {
        print "<b>Sorry, we ran into an error (#6543435456543)</b>";
        exit(1);
      }
      $dbh = null;
    } catch (PDOException $e) {
        print "<b>Sorry, we ran into an error (#13251843584654) " . $e->getMessage() . "<br/>";
        $dbh = null;
        exit(1);
    }

    $stocks_held .= '</tbody></table>';
    $stocks_held_dd .= '</select>';


    include_once('template/index.inc.php');
    exit();
