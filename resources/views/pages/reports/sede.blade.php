<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>{{__('Pedido')}}</title>
    <link rel="stylesheet" href="style.css" media="all" />
    <style type="text/css">
    	@font-face {
  font-family: SourceSansPro;
  src: url(SourceSansPro-Regular.ttf);
}

.clearfix:after {
  content: "";
  display: table;
  clear: both;
}

a {
  color: #0087C3;
  text-decoration: none;
}

body {
  position: relative;
  width: 19cm;
  height: 29.7cm;
  margin: 0 auto;
  color: #555555;
  background: #FFFFFF;
  font-family: Arial, sans-serif;
  font-size: 14px;
  font-family: SourceSansPro;
}

header {
  padding: 2px 0;
  border-bottom: 1px solid #AAAAAA;
}

#logo {
  text-align: center;
}

#logo img {
  height: 150px;
  width: 150px;
}

#company {
  float: right;
  text-align: right;
}


#details {
  padding-bottom: 15px;
  border-bottom: 1px solid #AAAAAA;
}

#client {
  padding-left: 6px;
  float: left;
}

#client .to {
  color: #777777;
  font-size:16px;
}

#invoice .to {
  color: #777777;
  font-size:16px;
}

h2.name {
  font-size: 1.4em;
  font-weight: normal;
  margin: 0;
}

#invoice {
  margin-right: 50px;
  padding-right: 50px;
  text-align: right;
}


#invoice .date {
  font-size: 1.1em;
  color: #777777;
}

table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 20px;
  margin-top: 10px;
}

table th,
table td {
  padding: 5px;
  background: #EEEEEE;
  text-align: center;
  border-bottom: 1px solid #FFFFFF;
}

table th {
  white-space: nowrap;
  font-weight: normal;
}

table td {
  text-align: center;
}

table td h3{
  color: #57B223;
  font-size: 1.2em;
  font-weight: normal;
  margin: 0 0 0.2em 0;
}

table .no {
  color: #FFFFFF;
  font-size: 14px;
  background: #57B223;
  width: 7px;
}

table .desc {
  text-align: left;
}

table .unit {
  background: #DDDDDD;
}

table .qty {
  font-size: 16px;
  width: 7%;
  color: #fff;
  background-color: #2592a1;
  margin: 0px !important;
}

table .qty2 {
   font-size: 14px;
   width: 4%;
   margin: 0px !important;
}



table tbody tr:last-child td {
  border: none;
}

table tfoot td {
  padding: 10px 20px;
  background: #FFFFFF;
  border-bottom: none;
  font-size: 1.2em;
  white-space: nowrap;
  border-top: 1px solid #AAAAAA;
}

table tfoot tr:first-child td {
  border-top: none;
}

table tfoot tr:last-child td {
  color: #2592a1;
  font-size: 1.4em;
  border-top: 1px solid #57B223;

}

table tfoot tr td:first-child {
  border: none;
}

#thanks{
  font-size: 2em;
  margin-bottom: 30px;
}

#thanks2{
  font-size: 1em;
  margin-bottom: 10px;
}

#notices{
  padding-left: 6px;
  border-left: 6px solid #0087C3;
}

#notices .notice {
  font-size: 1.2em;
}

footer {
  color: #777777;
  width: 100%;
  height: 30px;
  position: absolute;
  bottom: 0;
  border-top: 1px solid #AAAAAA;
  padding: 8px 0;
  text-align: center;
}


    </style>
  </head>
   <body>
    <header class="clearfix">
      <div id="logo">
        <img src="{{ public_path('img/logo.png') }}">
      </div>

      </div>
    </header>
    <main>
     <div id="thanks">Datos del Pedido</div>
      <div id="details" class="clearfix">
        <div id="client">
          <div class="to">{{__('Proveedor')}}:</div>

        </div>
        <div id="invoice">
         <div class="to"> {{__('Estatus')}}: <strong></strong></div>
        </div>
      </div>

      <div id="details" class="clearfix">
        <div id="client">
          <div class="to">{{__('Pedido')}}:<strong></strong></div>
         <div class="to">{{__('Destinatario')}}:<strong></strong></div>
         <div class="to">{{__('Dirección')}}:<strong></strong></div>
         <div class="to">{{__('Localidad')}}:<strong></strong></div>
          <div class="to">{{__('Provincia')}}:<strong></strong></div>
        </div>
        <div id="invoice">
            <div class="to"> {{__('Tasación')}}: <strong></strong></div>
            <div class="to"> {{__('Contacto')}}: <strong></strong></div>
            <div class="to"> {{__('Código postal')}}: <strong></strong></div>
            <div class="to"> {{__('Perito')}}: <strong></strong></div>
             <div class="to"> {{__('Notas')}}: <strong></strong></div>
        </div>

        </div>

       <div id="details" class="clearfix">
        <div id="thanks2"><h3>Datos del vehí­culo</h3></div>
        <div id="client">
          <div class="to">{{__('Matrí­cula')}}:<strong></strong></div>
         <div class="to">{{__('Marca')}}:<strong></strong></div>
         <div class="to">{{__('Modelo')}}:<strong></strong></div>
         <div class="to">{{__('Versión')}}:<strong></strong></div>

        </div>
        <div id="invoice">
            <div class="to"> {{__('Tipo')}}: <strong></strong></div>
            <div class="to"> {{__('VIN')}}: <strong></strong></div>
            <div class="to"> {{__('Km')}}: <strong></strong></div>
            <div class="to"> {{__('Renting')}}: <strong></strong></div>

        </div>

        </div>

      <table border="0" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th class="qty">Referencia</th>
            <th class="qty">Descripción</th>
            <th class="qty">Cantidad</th>
            <th class="qty">Precio</th>
            <th class="qty">Dto(%)</th>
            <th class="qty">Precio Neto</th>
            <th class="qty">{{__('Plazo Entrega (días)')}}</th>
           <th class="qty"></th>
          </tr>
        </thead>
        <tbody>
                <tr>
                    <td class="qty2"></td>
                    <td class="qty2"></td>
                    <td class="qty2"></td>
                    <td class="qty2"></td>
                    <td class="qty2"></td>
                    <td class="qty2"></td>
                    <td class="qty2"></td>
                </tr>

        </tboody>
        <tfoot>
          <tr>
            <td colspan="3"></td>
            <td colspan="2"></td>
            <td colspan="1">SUTOTAL</td>
            <td></td>
          </tr>
          <tr>
            <td colspan="3"></td>
            <td colspan="2"></td>
            <td colspan="1">PORTE</td>
            <td></td>
          </tr>
          <tr>
            <td colspan="3"></td>
            <td colspan="2"></td>
            <td colspan="1">TOTAL</td>
            <td></td>
          </tr>
        </tfoot>
      </table>

    </main>

  </body>
</html>

