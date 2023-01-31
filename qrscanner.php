<?php
  ob_start();
  session_start();
  include('inc/header.php');
  include 'Inventory.php';
  $inventory = new Inventory();
?>
<?php
  function displayQrScanner() {
?>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/purchase.js"></script>
<script src="js/product2.js"></script>
<script src="js/common.js"></script>
<?php include('inc/container.php'); ?>
<?php include("menus.php"); ?>
<script src="./html5-qrcode.min.js"></script>
<style>
    .result{
    background-color: green;
    color:#fff;
    padding:20px;
    }
    .row{
    display:flex;
    }
</style>
  
    <div class="row">
      <div class="col">
        <div style="width:500px;" id="reader"></div>
      </div>
      <div class="col" style="padding:30px;">
        <h4>SCAN RESULT</h4>
        <div id="result">Result Here</div>
          <div class="btn-group btn-group-justified">
            <button type="button" name="" id="" class="btn btn-primary "> Retirar herramientas</button>
            <button type="button" name="" id="" class="btn-sm rounded-0" >Devolver herramientas</button>
            
        </div>
        <body>
            <?php $inventory = new Inventory();
            $productNames = $inventory->getPtNames();
            ?>
            <div class="form-group">
                <label>Personal</label>
                <form action="">
                    <select name="supplier" id="supplier" class="form-select rounded-0" required>
                        <?php foreach($productNames as $product){ ?>
                        <option value="">
                            <?php echo $product['supplier_name']; ?>
                        </option>
                        <?php } ?>
                    </select>
                </form>
                
            </div>
        <body>
      </div>
      
    </div>
    

  <?php
  }
?>
<?php displayQrScanner(); ?>

<div class="container">
        
        <?php $inventory = new Inventory();
        $productNames = $inventory->getPurchaseNames();

        foreach($productNames as $product){
    
            echo $product['pid']."<br>";
        }
        if(isset($_SESSION['userid'])){
            echo "Bienvenido, " . $_SESSION['name'];
          }
        ?>
        
        <script type="text/javascript">
          
          var qrCodeData = [];
          function onScanSuccess(qrCodeMessage) {
              //document.getElementById('result').innerHTML = '<span class="result">'+qrCodeMessage+'</span>'; 
            var proid = <?php echo json_encode($inventory->getPurchaseNames()); ?>;
            
            var productList = <?php echo json_encode($inventory->getProductNames()); ?>;
            var exists = false;
            //$proid = array_map('intval', <?php echo json_encode($inventory->getPurchaseNames()); ?>);
            console.log(qrCodeData.indexOf(qrCodeMessage));
            if (qrCodeData.indexOf(qrCodeMessage) === -1) {
                
                for (var i = 0; i < productList.length; i++) {
                    if (productList[i].pname == qrCodeMessage) {
                    
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', 'add_purchase.php', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                        console.log(this.responseText);
                        }
                        };
                        
                        xhr.send('product=' + (proid[i].pid) + '&quantity=1&supplierid=' + 3);
                        exists = true;
                        break;
                    
                    }
                }
            }
            qrCodeData.push(qrCodeMessage);
            
            if (exists) {  
              document.getElementById('result').innerHTML = '<span class="result">Existe</span>'; 
            } else {
              document.getElementById('result').innerHTML = '<span class="result">No existe</span>'; 
            }
            
        }

          function onScanError(errorMessage) {
            //handle scan error
          }
          var html5QrcodeScanner = new Html5QrcodeScanner(
              "reader", { fps: 10, qrbox: 250 });
          html5QrcodeScanner.render(onScanSuccess, onScanError);
        </script>
    </body>
<div class="container">

<div class="row">
    <div class="col-lg-12">
        <div class="card card-default rounded-0 shadow">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                        <h3 class="card-title">Material que usas</h3>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 text-end">
                        <button type="button" name="addPurchase" id="addPurchase" class="btn btn-primary btn-sm rounded-0"><i class="far fa-plus-square"></i> Agregar Compra</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        <table id="purchaseList" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Proveedor</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                        </table>
                        <div class="row">
                        <h3 class="card-title">Material usado total</h3>
                        <table id="productListInactive" class="table table-bordered table-striped">
                          <thead>
                              <tr>
                                  <th>ID</th>
                                  <th>Nombre de Producto</th>
                                  <th>Cantidad total</th>
                                  <th>Personal</th>
                                  <th>Estado</th>
                                  <th>Editar</th>                   
                              </tr>
                          </thead>
                      </table>

                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="purchaseModal" class="modal fade">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <form method="post" id="purchaseForm">
                        <input type="hidden" name="purchase_id" id="purchase_id" />
                        <input type="hidden" name="btn_action" id="btn_action" />
                        <div class="form-group">
                            <label>Nombre de Material</label>
                            <select name="product" id="product" class="form-select rounded-0" required>
                                <option value="">Selecciona Material</option>
                                <?php echo $inventory->productDropdownList(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Cantidad de Material</label>
                            <div class="input-group">
                                <input type="text" name="quantity" id="quantity" class="form-control rounded-0" required pattern="[+-]?([0-9]*[.])?[0-9]+" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Personal</label>
                            <select name="supplierid" id="supplierid" class="form-select rounded-0" required>
                                <option value="">Selecciona Personal</option>
                                <?php echo $inventory->supplierDropdownList(); ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <input type="submit" name="action" id="action" class="btn btn-primary btn-sm rounded-0" value="Agregar" form="purchaseForm" />
                    <button type="button" class="btn btn-default border btn-sm rounded-0" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div id="productViewModal" class="modal fade">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" id="product_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><i class="fa fa-th-list"></i> Información de Material</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <Div id="productDetails"></Div>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
