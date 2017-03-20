
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="assets/img/favicon.ico">
    <title>Stockman | Manage Stock Portfolio</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="assets/css/starter-template.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>          
          <a class="navbar-brand" href="index.php"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> Stocks Manager App</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <!-- <li class="active"><a href="#">Buy</a></li>
            <li><a href="#about">Sell</a></li>
            <li><a href="#contact">Add Cash</a></li>
          </ul> -->
        </div><!--/.nav-collapse -->
      </div>
    </nav>
    <div class="container">
      <div class="starter-template">
        <font face="arial" size="14" color="gray"><b>[Your balance: <?php if( isset($user_bal) ) { print htmlspecialchars(print_amount($user_bal)); } else { print "$0.00"; } ?>]</b></font><br /><br />
        <p class="lead">
        </p>
      </div>
      <div id="topmessage"><?php if(isset($topmessage)) print $topmessage; ?></div>
      <table><tr><td><button type="button" class="btn btn-primary btn-lg" data-toggle="modal" href="#buystock">[Buy]</button>&nbsp;&nbsp;</td><td><button type="button" class="btn btn-primary btn-lg" data-toggle="modal" href="#sellstock">[Sell]</button>&nbsp;&nbsp;</td><td><button type="button" class="btn btn-primary btn-lg" data-toggle="modal" href="#addcash">[Add Cash]</button></td></tr></table>
      <hr />
      <!-- <table class="table table-hover">        
        <thead> 
          <tr><th> # </th> <th>Stock</th> <th>Amount held</th> </tr> 
        </thead> 
        <tbody> 
          <tr> <th scope=row>1</th> <td>AAPL</td> <td>65485</td> </tr> 
          <tr> <th scope=row>2</th> <td>AMZN</td> <td>65456</td> </tr>
          <tr> <th scope=row>3</th> <td>FB</td> <td>9875</td> </tr> 
        </tbody>
      </table> -->
      <?php if(isset($stocks_held)) print $stocks_held;  ?>
    </div><!-- /.container -->

    <!-- Add cash popup -->
    <div class="modal fade" id="addcash" tabindex="-1" role="dialog" aria-labelledby="addcashmodallabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><b><font color="green"> | Add Cash to Account | </font></b></h4>
          </div>
          <div class="modal-body">
          Enter a cash amount to add to your stocks account <br /><br />
            <form role="form" name="addcashform" class="well" action="addcash.php" >
              <div id="addcasherror"></div>
                <label>Amount: </label>
                <div class="input-group">
                <span class="input-group-addon">$</span>
                <input class="form-control" id="casham" type="text" name="cashamount" length="9" placeholder="e.g. 2000">
                </div>
              </form>
            <button type="button" id="addcashbutton" class="btn btn-primary">Add</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Buy stock popup -->
    <div class="modal fade" id="buystock" tabindex="-1" role="dialog" aria-labelledby="buystockmodallabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><b><font color="green"> | Buy Stock | </font></b></h4>
          </div>
          <div class="modal-body">
          Enter a stock code for a price check, and an amount of stock to purchase at that price. <br /><br />
            <form role="form" name="buystockform" class="well" action="buystock.php">
              <div id="buystockerror"></div>
                <label>Stock code: </label>
                <div class="input-group">
                <input class="form-control" id="buystockcode" autocomplete="off" type="text" name="buystockcode" placeholder="e.g. AMZN" oninput="validateBuyPrice()"><br /><br /><input class="btn btn-info" type="button" id="btncheckprice" name="btncheckprice" value="Check Price" disabled="disabled"><div id="buystockprice"></div>
                <input type="hidden" name="buystockprice" id="buystockprice" value="">
                </div><br />
                <label>Amount to buy: </label>
                <div class="input-group">
                <input class="form-control" id="buystockamount" type="text" name="buystockamount" placeholder="e.g. 200">
                </div>
              </form>
            <button type="button" id="buystockbutton" name="buystockbutton" class="btn btn-primary" disabled="disabled">Buy</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Sell stock popup -->
    <div class="modal fade" id="sellstock" tabindex="-1" role="dialog" aria-labelledby="sellstockmodallabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><b><font color="green"> | Sell Stock | </font></b></h4>
          </div>
          <div class="modal-body">
          Select a stock code for a price check, and an amount of that stock to sell at that price. <br /><br />
            <form role="form" name="sellstockform" class="well" action="sellstock.php" >
              <div id="sellstockerror"></div>
                <label>Stock code: </label>
                <div class="input-group">
                <?php if(isset($stocks_held_dd)) print $stocks_held_dd; ?>
                  <br /><br /><input class="btn btn-info" id="btnsellcheckprice" name="btnsellcheckprice" type="button" value="Check Price" disabled="disabled"><div id="sellstockprice"></div>
                  <input type="hidden" name="sellstockprice" id="sellstockprice" value="">
                </div><br />
                <label>Amount to sell: </label>
                <div class="input-group">
                <input class="form-control" id="sellstockamount" type="text" name="sellstockamount" placeholder="e.g. 300">
                </div>
              </form>
            <button type="button" id="sellstockbutton" class="btn btn-primary" disabled="disabled">Sell</button>
          </div>
        </div>
      </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/ie10-viewport-bug-workaround.js"></script>
    <script type="text/javascript">
      // initialize add cash popup on launch
      $('#addcash').on('shown.bs.modal', function (e) {
        $('#topmessage').html('');
        $('#casham').val('');
        $('#casham').focus();
      });

      // initialize buy stock popup on launch
      $('#buystock').on('shown.bs.modal', function (e) {
        $('#topmessage').html('');
        $('#buystockcode').val('');
        $('#buystockcode').focus();
        $('#buystockerror').html('');
        $('#buystockprice').html('');        
        $('#buystockamount').val('');
        $('#buystockcode').removeAttr("readOnly");
        $('#buystockbutton').attr("disabled", "disabled");
      });

      // initialize sell stock popup on launch
      $('#sellstock').on('shown.bs.modal', function (e) {
        $('#topmessage').html('');
        $('#sellstockcode').val('');
        $('#sellstockcode').focus();
        $('#sellstockerror').html('');
        $('#sellstockprice').html('');        
        $('#sellstockamount').val('');
        $('#sellstockcode').removeAttr("readOnly");
        $('#sellstockbutton').attr("disabled", "disabled");
      });

      // action on typing a stock code on buy stock popup
      function validateBuyPrice() {
        if($('#buystockcode').val().trim() !== ""){
          $('#btncheckprice').removeAttr("disabled");
        } else {
          $('#btncheckprice').attr("disabled", "disabled");
          $('#sellstockbutton').attr("disabled", "disabled");
        }
      }

      // action on selecting a stock code on sell stock popup
      function validateSellPrice() {
        if($('#sellstockcode').val().trim() !== ""){
          $('#btnsellcheckprice').removeAttr("disabled");
        } else {
          $('#btnsellcheckprice').attr("disabled", "disabled");
          $('#buystockbutton').attr("disabled", "disabled");
        }
      }

      //--------------------SELL---------------------------------
      // action on clicking check price button on sell stock popup
      $('#btnsellcheckprice').click(function () {
        var stockcode = $('#sellstockcode').val().trim();

        if( stockcode === "" ){
          $('#sellstockerror').html("<div class='alert alert-danger'>Please select a valid stock symbol.</div>");
          return;
        }

        $('#btnsellcheckprice').attr("value", "Please wait...");
        $('#btnsellcheckprice').attr("disabled", "disabled");

        $.post('sellstock.php', {
          'sellstockcode': stockcode,
          'checkprice': stockcode
        }, function (sdata, sstatus){
          var sdata_array = sdata.split("|");
          var sdata_type = sdata_array[0];
          var sdata_data = sdata_array[1];
          var num_price = sdata_array[2];

          if( sdata_type === "m" ) {
            // display error on buy stock popup
            $('#sellstockerror').html("<div class='alert alert-danger'>" + sdata_data + "</div>");
            //Recaptcha.reload();
          } else if( sdata_type === "s" ){
            // price returned: show on buy stock popup    
            $('#sellstockprice').html("&nbsp;&nbsp;<span class='label label-success'><font size='2'>" + sdata_data + "</font></span>");
            $('#sellstockcode').attr("readOnly", "true");
            $('#sellstockbutton').removeAttr("disabled");
            $('#sellstockamount').focus();
            $('#btnsellcheckprice').attr("value", "Check Price");
            $('#sellstockprice').val(num_price);
            // $('#btncheckprice').removeAttr("disabled");
          }
        });
      });

      // action on clicking Sell on sell stock popup
      $('#sellstockbutton').click(function () {
        var stockcode = $('#sellstockcode').val().trim();
        var amount = $('#sellstockamount').val().trim();
        var sellprice = $('#sellstockprice').val().trim();

        // alert(stockcode + " | " + amount + " | " + buyprice); return;
        // alert(parseInt(amount,10)); return;

        if( stockcode === "" || amount === "" || !Number.isInteger(parseInt(amount,10)) ){
          $('#buystockerror').html("<div class='alert alert-danger'>Please enter a valid stock code and purchase amount.</div>");
          return;
        }

        $('#buystockbutton').attr("value", "Please wait...");

        $.post('sellstock.php', {
          'sellstockcode': stockcode,
          'sellstockamount': amount,
          'sellprice': sellprice,
          'sellstock': stockcode
        }, function (sdata, sstatus){
          var sdata_array = sdata.split("|");
          var sdata_type = sdata_array[0];
          var sdata_data = sdata_array[1];

          if( sdata_type === "m" ) {
            // display error on buy stock popup
            $('#sellstockerror').html("<div class='alert alert-danger'>" + sdata_data + "</div>");
            //Recaptcha.reload();
          } else if( sdata_type === "r" ){
            // purchase successful - reload app and show message
            window.location.replace("index.php?ref=stocksell");
            return;
          }
        });
      });

      //----------------------------BUY---------------------------------------

      // action on clicking Buy on buy stock popup
      $('#buystockbutton').click(function () {
        var stockcode = $('#buystockcode').val().trim();
        var amount = $('#buystockamount').val().trim();
        var buyprice = $('#buystockprice').val().trim();

        // alert(stockcode + " | " + amount + " | " + buyprice); return;
        // alert(parseInt(amount,10)); return;

        if( stockcode === "" || amount === "" || !Number.isInteger(parseInt(amount,10)) ){
          $('#buystockerror').html("<div class='alert alert-danger'>Please enter a valid stock code and purchase amount.</div>");
          return;
        }

        $('#buystockbutton').attr("value", "Please wait...");

        $.post('buystock.php', {
          'buystockcode': stockcode,
          'buystockamount': amount,
          'buyprice': buyprice,
          'buystock': stockcode
        }, function (sdata, sstatus){
          var sdata_array = sdata.split("|");
          var sdata_type = sdata_array[0];
          var sdata_data = sdata_array[1];

          if( sdata_type === "m" ) {
            // display error on buy stock popup
            $('#buystockerror').html("<div class='alert alert-danger'>" + sdata_data + "</div>");
            //Recaptcha.reload();
          } else if( sdata_type === "r" ){
            // purchase successful - reload app and show message
            window.location.replace("index.php?ref=stockbuy");
            return;
          }
        });
      });

      // action on clicking check price button on buy stock popup
      $('#btncheckprice').click(function () {
        var stockcode = $('#buystockcode').val().trim();

        if( stockcode === "" ){
          $('#buystockerror').html("<div class='alert alert-danger'>Please enter a valid stock symol.</div>");
          return;
        }

        $('#btncheckprice').attr("value", "Please wait...");
        $('#btncheckprice').attr("disabled", "disabled");

        $.post('buystock.php', {
          'buystockcode': stockcode,
          'checkprice': stockcode
        }, function (sdata, sstatus){
          var sdata_array = sdata.split("|");
          var sdata_type = sdata_array[0];
          var sdata_data = sdata_array[1];
          var num_price = sdata_array[2];

          if( sdata_type === "m" ) {
            // display error on buy stock popup
            $('#buystockerror').html("<div class='alert alert-danger'>" + sdata_data + "</div>");
            //Recaptcha.reload();
          } else if( sdata_type === "s" ){
            // price returned: show on buy stock popup    
            $('#buystockprice').html("&nbsp;&nbsp;<span class='label label-success'><font size='2'>" + sdata_data + "</font></span>");
            $('#buystockcode').attr("readOnly", "true");
            $('#buystockbutton').removeAttr("disabled");
            $('#buystockamount').focus();
            $('#btncheckprice').attr("value", "Check Price");
            $('#buystockprice').val(num_price);
            // $('#btncheckprice').removeAttr("disabled");
          }
        });
      });

      //-----------------------CASH-----------------------------------
      // action on clicking Add button on Add Cash popup
      $('#addcashbutton').click(function () {
        var addamount = $('#casham').val().trim();

        if( addamount === "" || addamount <=0 || !$.isNumeric(addamount) ){
          $('#addcasherror').html("<div class='alert alert-danger'>Please enter a valid cash amount.</div>");
          return;
        }

        $.post('addcash.php', {
          'addcashamount': addamount
        }, function (sdata, sstatus){
          //alert("Return value from add cash service: " + sdata);
          //Capture and separate (searchtype | result)-format server response
          var sdata_array = sdata.split("|");
          var sdata_type = sdata_array[0];
          var sdata_data = sdata_array[1];

          if( sdata_type === "m" ) {
            $('#addcasherror').html("<div class='alert alert-danger'>" + sdata_data + "</div>");
            //Recaptcha.reload();
          } else if( sdata_type === "r" ){            
            window.location.replace("index.php?ref=cashadd");
            return;
          }
        });
      });
    </script>
  </body>
</html>
