<?php
$root = realpath($_SERVER['DOCUMENT_ROOT']);
// includes have been moved removed for security reasons

$store_array = $_SESSION['atk_store_array'];
setCookie('dashboard_locations', $store_array, 0, "/");
$_COOKIE['dashboard_locations'] = $store_array;

$get_warehouses = mysqli_query($db_handle, "SELECT number FROM ldap.store_listing WHERE store_dc = 'warehouse'");
while ($warehouses_rows = mysqli_fetch_array($get_warehouses)) {
    $warehouses .= $warehouses_rows['number'] . ",";
}

$warehouses = substr($warehouses, 0, -1);

setcookie('dashboard_warehouses', $warehouses, 0, "/");


$get_same_stores_financial = mysqli_query($db_handle, 'SELECT number FROM ldap.store_listing WHERE comp = 1');
while ($same_store_financial_rows = mysqli_fetch_array($get_same_stores_financial)) {
    $same_store_financial .= $same_store_financial_rows['number'] . ",";
}

$same_store_financial = substr($same_store_financial, 0, -1);

setcookie('dashboard_same_store_financial', $same_store_financial, 0, "/");


$ly_today = strtotime('-1 year', strtotime('yesterday'));
$yesterday = date('Y-m-d', strtotime('yesterday'));
$ly_yesterday = date('Y-m-d', ly_date(strtotime('yesterday')));
$mtd = date('Y-m-d', strtotime('first day of this month', strtotime($yesterday)));
$ly_mtd = date('Y-m-d', strtotime('first day of this month', $ly_today));
$ytd = date('Y-m-d', strtotime('1/1 this year'));
$ly_ytd = date('Y-m-d', strtotime('1/1 this year', $ly_today));
$rolling7 = date('Y-m-d', strtotime('-8 days', strtotime('yesterday')));
$ly_rolling7 = date('Y-m-d', strtotime('-8 days', ly_date(strtotime('yesterday'))));
$rolling4 = date('Y-m-d', strtotime('-29 days', strtotime('yesterday')));
$ly_rolling4 = date('Y-m-d', strtotime('-29 days', ly_date(strtotime('yesterday'))));
$ly_today = date('Y-m-d', $ly_today);
$year = date('Y')

?>
<head>

    <!-- Created by Isaac Staub-->
    <!---->
    <!--    (╯°□°)╯︵ ┻━┻-->
    <!---->
    <!--    ┬─┬ノ( ゜-゜ノ)-->
    <!---->
    <!--    (╯°Д°）╯︵ /(.□ . \)-->

    <title>Product Manager</title>

    <input type="hidden" id="yesterday" value="<?php echo $yesterday ?>">
    <input type="hidden" id="ly_yesterday" value="<?php echo $ly_yesterday ?>">
    <input type="hidden" id="mtd" value="<?php echo $mtd ?>">
    <input type="hidden" id="ly_mtd" value="<?php echo $ly_mtd ?>">
    <input type="hidden" id="ytd" value="<?php echo $ytd ?>">
    <input type="hidden" id="ly_ytd" value="<?php echo $ly_ytd ?>">
    <input type="hidden" id="ly_yest_ac" value="<?php echo $ly_today ?>">
    <input type="hidden" id="year" value="<?php echo $year ?>">
    <input type="hidden" id="s_location" value="<?php echo $store_array ?>">
    <input type="hidden" id="rolling7" value="<?php echo $rolling7 ?>">
    <input type="hidden" id="ly_rolling7" value="<?php echo $ly_rolling7 ?>">
    <input type="hidden" id="rolling4" value="<?php echo $rolling4 ?>">
    <input type="hidden" id="ly_rolling4" value="<?php echo $ly_rolling4 ?>">
</head>

<link href='/includes/css/rk-theme.css' rel='stylesheet' type='text/css'/>
<script type='text/javascript' src='constants.js'></script>
<script type='text/javascript' src='invo_tab.js'></script>
<script type='text/javascript' src='supply_tab.js'></script>
<script type='text/javascript' src='daily_sales_tab.js'></script>
<script type='text/javascript' src='rolling_7_sales.js'></script>
<script type='text/javascript' src='month_sales_tab.js'></script>
<script type='text/javascript' src='rolling_4_sales.js'></script>
<script type='text/javascript' src='yearly_sales_tab.js'></script>
<script type='text/javascript' src='sales_chart.js?v=1'></script>
<!--    <script type='text/javascript' src='inventory_analysis.js'></script>-->
<script type='text/javascript' src='rep_tab.js'></script>
<!--<script type='text/javascript' src='cycle_tab.js'></script>-->
<script type='text/javascript' src='po_tab.js'></script>
<script type='text/javascript' src='uda_tab.js'></script>
<script type='text/javascript' src='metrics_tab.js?v=2'></script>


<body class='default'>

<!--Search bar and buttons-->

<div id="sku_search">
    <input type="text" placeholder="Search Sku" id="sku_input">
    <button onclick="searchSKU(); clear();" id="sku_button">Search</button>
    <select onchange="stateSelect()" default-placeholder="All Stores" id="state_change">
        <option value="state">All Stores</option>
        <option value="AL">Alabama</option>
        <option value="FL">Florida</option>
        <option value="IL">Illinois</option>
        <option value="IN">Indiana</option>
        <option value="KY">Kentucky</option>
        <option value="MO">Missouri</option>
        <option value="NC">North Carolina</option>
        <option value="OH">Ohio</option>
        <option value="PA">Pennsylvania</option>
        <option value="TN">Tennessee</option>
        <option value="VA">Virginia</option>
        <option value="WV">West Virginia</option>
    </select>
    <a href='/home.php'><img id='rklogo' alt='rk-logo' href='/home.php' src='/images/Logo_2021.png'
                             style='float: right; height: 50px; display: inline-block; margin-left: 25px; margin-right: 25px; vertical-align: middle;'></a>
</div>

<br>

<div>
    <button onclick="exportstuff()">Export to Excel</button>
    <button onclick="hideTable()">Hide/Show Table</button>
</div>

<br>

<!--sku number display-->
<div id="sku_div">
    Showing Info for SKU #: <span id="sku_div_sku"></span>
</div>

<br>

<!--static data display-->
<table id="top_table">
    <tr>
        <th scope='row'>UPC</th>
        <td><input type="text" id="upc" disabled></td>
        <th scope='row'>Item Description</th>
        <td><input type="text" id="item_description" disabled></td>
        <th scope='row'>UOM</th>
        <td><input type="text" id="stocking_unit" disabled></td>
        <th scope="row">Sellable Indicator</th>
        <td><input type="text" id="sellable_ind" disabled></td>
        <th scope='row'>Planogram:</th>
        <td id="planogram"></td>
        <td rowspan="5"><img src="#" id="sku_image" style="float: right" height="150 px"
                             onerror="this.onerror=null;this.src='/images/no_image.jpg';"></td>
    </tr>
    <tr>
        <th scope='row'>Department</th>
        <td><input type="text" id="dept_name" disabled></td>
        <th scope='row'>Vendor Name</th>
        <td><input type="text" id="vendor_name" disabled></td>
        <th scope='row'>Buyer Name</th>
        <td><input type="text" id="buyer_name" disabled></td>
        <th scope="row">Orderable Indicator</th>
        <td><input type="text" id="orderable_ind" disabled></td>
    </tr>
    <tr>
        <th scope='row'>Class</th>
        <td><input type="text" id="class_name" disabled></td>
        <th scope="row">Case Qty</th>
        <td><input type="text" id="case_qty" disabled></td>
        <th scope="row">MAP</th>
        <td><input type="text" id="map" disabled></td>
    </tr>
    <tr>
        <th scope='row'>Subclass</th>
        <td><input type="text" id="fineline_name" disabled></td>
        <th scope='row'>MFG Part Number</th>
        <td><input type="text" id="mfg_part_number" disabled></td>
        <th scope='row'>Inventory Indicator</th>
        <td><input type="text" id="keep_stock_data" disabled></td>
    </tr>
</table>


<!--<div id="page_modal">-->
<!--</div>-->

<br>


<!--jqxtabs-->
<div id='jqxTabs'>
    <ul style="margin-left:50px;">
        <!--        <li>Inventory Analysis</li>-->
        <li>Daily Sales</li>
        <li>Rolling 7 Sales</li>
        <li>Monthly Sales</li>
        <li>Rolling 4 sales</li>
        <li>Yearly Sales</li>
        <li>Inventory Info</li>
        <li>Supplier Info</li>
        <li>Replenishment</li>
        <li>Purchase Orders</li>
        <li>UDA</li>
        <li>Metrics</li>
        <!--        <li>Cycle Count</li>-->
        <li>Sales Chart</li>
    </ul>

    <!--    <div id="inventory_analysis_tab"></div>-->
    <div id="daily_sales_tab"></div>
    <div id="rolling_7_sales"></div>
    <div id="monthly_sales_tab"></div>
    <div id="rolling_4_sales"></div>
    <div id="yearly_sales_tab"></div>
    <div id="inv_tab"></div>
    <div id="jqxgrid_sup"></div>
    <div id="rep_tab"></div>
    <div id="po_tab"></div>
    <div id="uda_tab"></div>
    <div id="metrics_tab"></div>
    <!--    <div id="cycle_tab"></div>-->
    <div id="sales_chart"></div>


    <div id="jqxgrid"></div>

</div>
<div id="loading_spinner"></div>
</body>
<script type="text/javascript">

    console.log(window.matchMedia);
    let isDarkReaderEnabled = "querySelector" in document && !!document.querySelector("meta[name=darkreader]");
    if ((window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) || isDarkReaderEnabled) {
        // dark mode
        console.log('dark mode');
        let logo = document.getElementById('rklogo');
        logo.src = '/images/Logo_2021_darkmode.png';
    }

    function hideTable() {
        let x = document.getElementById("top_table");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }

    function stateSelect() {
        stateFilter($('#daily_sales_tab'))
        stateFilter($('#rolling_7_sales'))
        stateFilter($('#monthly_sales_tab'))
        stateFilter($('#rolling_4_sales'))
        stateFilter($('#yearly_sales_tab'))
        stateFilter($('#inv_tab'))
        stateFilter($('#rep_tab'))
    }

    function stateFilter(grid) {
        let state = $('#state_change').val()
        let currentFilter = grid.jqxGrid('getfilterinformation');
        if (currentFilter.length > 0) {
            grid.jqxGrid('clearfilters');
        }
        if (state !== 'state') {
            let filtergroup = new $.jqx.filter();
            let filtervalue = state;
            let filtercondition = 'equal';
            let filter = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
            // add the filters.
            filtergroup.addfilter(0, filter);
            // apply the filters.
            grid.jqxGrid('addfilter', 'State', filtergroup);
            grid.jqxGrid('applyfilters');
        }
    }

    $(document).ready(function () {


        document.getElementById("sku_input").addEventListener("keyup", function (event) {
            event.preventDefault();
            if (event.keyCode === 13) {
                document.getElementById("sku_button").click();
            }
        });


        $('#jqxTabs').jqxTabs({height: '100%', width: '100%', theme: theme});

        $('#jqxTabs').on('selected', function (event) {
            clickedstuff(event.args.item);
        })


    })
    ;


    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
    }


    function searchSKU() {
        // $('#sku_search_dialog').dialog('close');
        let sku = $('#sku_input').val();
        setCookie('sku_management', sku);
        setCookie('dashboard_skus', sku);
        // setCookie('dashboard_class', sku);
        // setCookie('deshboard_department', sku)
        $('#sku_div').css('display', 'block');
        // $('#skusearchbtn').css('display', 'none');
        $('#revertskusearchbtn').css('display', 'inline-block');


        let source =
            {
                dataType: "json",
                dataFields: [
                    {name: 'sku', type: 'string'},
                    {name: 'upc', type: 'string'},
                    {name: 'store_num', type: 'int'},
                    {name: 'item_description', type: 'string'},
                    {name: 'dept_code', type: 'string'},
                    {name: 'dept_name', type: 'string'},
                    {name: 'class_code', type: 'string'},
                    {name: 'class_name', type: 'string'},
                    {name: 'fineline_code', type: 'string'},
                    {name: 'fineline_name', type: 'string'},
                    {name: 'mfg_part_number', type: 'string'},
                    {name: 'vendor_name', type: 'string'},
                    {name: 'vendor_number', type: 'string'},
                    {name: 'buyer_code', type: 'string'},
                    {name: 'buyer_name', type: 'string'},
                    {name: 'quantity_on_hand', type: 'string'},
                    {name: 'quantity_on_order', type: 'string'},
                    {name: 'cost', type: 'string'},
                    {name: 'retail_price', type: 'string'},
                    {name: 'gross_margin', type: 'string'},
                    {name: 'gross_margin_percent', type: 'string'},
                    {name: 'stocking_unit', type: 'string'},
                    {name: 'keep_stock_data', type: 'string'},
                    {name: 'planogram', type: 'link'},
                    {name: 'inventory_ind', type: 'string'},
                    {name: 'sellable_ind', type: 'string'},
                    {name: 'orderable_ind', type: 'string'},
                    {name: 'pog', type: 'string'},
                    {name: 'map', type: 'string'},
                    {name: 'case_qty', type: 'string'},
                ],
                url: 'sku_mananager_info.php',
                root: 'Rows',
                data: {
                    sku: sku
                },
                cache: false,
            };


        let dataAdapter = new $.jqx.dataAdapter(source, {
            loadComplete: function (records) {
                let record = records.Rows;
                $('#sku_div_sku').text(record.sku);
                $("#upc").val(record.upc);
                $("#store_num").val(record.store_num);
                $("#item_description").val(record.item_description);
                // $("#dept_code").val(record.dept_code);
                $("#dept_name").val(record.dept_code + '  ' + record.dept_name);
                // $("#class_code").val(record.class_code);
                $("#class_name").val(record.class_code + '  ' + record.class_name);
                // $("#fineline_code").val(record.fineline_code);
                $("#fineline_name").val(record.fineline_code + '  ' + record.fineline_name);
                $("#mfg_part_number").val(record.mfg_part_number);
                $("#vendor_name").val(record.vendor_number + ' ' + record.vendor_name);
                // $("#vendor_number").val(record.vendor_number);
                // $("#buyer_code").val(record.buyer_code);
                $("#buyer_name").val(record.buyer_name + ' ' + record.buyer_code);
                $("#stocking_unit").val(record.stocking_unit);
                $("#inventory_ind").val(record.inventory_ind);
                $("#sellable_ind").val(record.sellable_ind);
                $("#orderable_ind").val(record.orderable_ind);
                $("#pog").val(record.pog);
                $("#map").val(record.map);
                $("#case_qty").val(record.case_qty);
                if (record.keep_stock_data) {
                    var inventory_ind = 'YES';
                } else {
                    var inventory_ind = 'NO';
                }
                $("#keep_stock_data").val(inventory_ind);
                if (record.planogram) {
                    var planlink = '<a target="_blank" href = ' + record.planogram + '>Link</a>';
                } else {
                    var planlink = 'No link available.';
                }
                document.getElementById('planogram').innerHTML = planlink;

                if (record.images) {
                    $("#sku_image").attr("src", record.images);
                } else {
                    $("#sku_image").attr("src", '/images/no_image.jpg');
                }


            }
        });
        let grid = $('#jqxgrid');
        grid.jqxGrid({source: dataAdapter});

        let cellsrenderer = function (row, columnfield, value, defaulthtml, columnproperties) {
        };

        clickedstuff(-1);

        clear();


    }

    function clear() {
        document.getElementById('sku_input').value = ''
    }

    function clickedstuff(position) {


        switch (position) {
            case 1:
                var tab = $('#rolling_7_sales');
                tab.jqxGrid('source')._source.url = '/content/dashboard/new_reports/sales/1_drill_by_store/sales.php';
                break;

            case 2:
                var tab = $('#monthly_sales_tab');
                tab.jqxGrid('source')._source.url = '/content/dashboard/new_reports/sales/1_drill_by_store/sales.php';
                break;

            case 3:
                var tab = $('#rolling_4_sales');
                tab.jqxGrid('source')._source.url = '/content/dashboard/new_reports/sales/1_drill_by_store/sales.php';
                break;

            case 4:
                var tab = $('#yearly_sales_tab');
                tab.jqxGrid('source')._source.url = '/content/dashboard/new_reports/sales/1_drill_by_store/sales.php';
                break;

            case 5:
                var tab = $('#inv_tab');
                tab.jqxGrid('source')._source.url = 'sku_mananager_inv.php';
                break;

            case 6:
                var tab = $('#jqxgrid_sup');
                tab.jqxGrid('source')._source.url = 'sku_mananager_supply.php';
                break;

            case 7:
                var tab = $('#rep_tab');
                tab.jqxGrid('source')._source.url = 'sku_manager_replenishment.php';
                break;

            case 8:
                var tab = $('#po_tab');
                tab.jqxGrid('source')._source.url = 'sku_manager_po.php';
                break;

            case 8:
                var tab = $('#uda_tab');
                tab.jqxGrid('source')._source.url = 'sku_manager_uda.php';
                break;

            case 10:
                var tab = $('#metrics_tab');
                tab.jqxGrid('source')._source.url = 'sku_manager_metrics.php';
                break;

            case 11:
                var tab = $('#sales_chart');
                tab.jqxChart('source')._source.url = '/content/dashboard/new_reports/sales/1_drill_by_store/month_sales.php';
                break;

            default:
                var tab = $('#daily_sales_tab');
                tab.jqxGrid('source')._source.url = '/content/dashboard/new_reports/sales/1_drill_by_store/sales.php';
                break;

        }
        $("#loading_spinner").show();
        console.log('shown');
        setTimeout(function() {
            tab.jqxGrid('source', tab.jqxGrid('source'));
            $("#loading_spinner").hide();
            console.log('hidden');
        }, 1);
    }


    function exportstuff() {
        //daily sales
        let grid = $('#daily_sales_tab');
        let exportData = grid.jqxGrid('exportdata', 'csv');
        let downloadLink;
        let dataType = 'text/csv';
        let id_to_position = {
            rolling_7_sales: 1,
            monthly_sales_tab: 2,
            rolling_4_tab: 3,
            yearly_sales_tab: 4,
            inv_tab: 5,
            jqxgrid_sup: 6,
            rep_tab: 7,
            po_tab: 8,
            uda_tab: 9,
            metrics_tab: 10,
            sales_chart: 11,
            default: -1
        };

        // Specify file name
        let filename = 'Daily_Sales.csv';

        // Create download link element
        downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);

        // Create a link to the file
        downloadLink.href = 'data:' + dataType + ', ' + exportData;
        // Setting the file name
        downloadLink.download = filename;
        //triggering the function
        downloadLink.click();


        //monthly sales
        let grid2 = $('#monthly_sales_tab');

        clickedstuff(id_to_position["monthly_sales_tab"]);

        let exportData2 = grid2.jqxGrid('exportdata', 'csv');
        let downloadLink2;
        let dataType2 = 'text/csv';

        // Specify file name
        let filename2 = 'Monthly_Sales.csv';

        // Create download link element
        downloadLink2 = document.createElement("a");
        document.body.appendChild(downloadLink2);

        // Create a link to the file
        downloadLink2.href = 'data:' + dataType2 + ', ' + exportData2;
        // Setting the file name
        downloadLink2.download = filename2;
        //triggering the function
        downloadLink2.click();


        //Yearly Sales
        let grid3 = $('#yearly_sales_tab');

        if (grid3.data('loaded') !== true) {
            clickedstuff(id_to_position["yearly_sales_tab"]);
        }

        let exportData3 = grid3.jqxGrid('exportdata', 'csv');
        let downloadLink3;
        let dataType3 = 'text/csv';


        // Specify file name
        let filename3 = 'Yearly_Sales.csv';

        // Create download link element
        downloadLink3 = document.createElement("a");
        document.body.appendChild(downloadLink3);

        // Create a link to the file
        downloadLink3.href = 'data:' + dataType3 + ', ' + exportData3;
        // Setting the file name
        downloadLink3.download = filename3;
        //triggering the function
        downloadLink3.click();

        //supply tab
        let grid4 = $('#jqxgrid_sup');

        if (grid4.data('loaded') !== true) {
            clickedstuff(id_to_position["jqxgrid_sup"]);
        }

        let exportData4 = grid4.jqxGrid('exportdata', 'csv');
        let downloadLink4;
        let dataType4 = 'text/csv';

        // Specify file name
        let filename4 = 'Supply.csv';

        // Create download link element
        downloadLink4 = document.createElement("a");
        document.body.appendChild(downloadLink4);

        // Create a link to the file
        downloadLink4.href = 'data:' + dataType4 + ', ' + exportData4;
        // Setting the file name
        downloadLink4.download = filename4;
        //triggering the function
        downloadLink4.click();

        //rep tab
        let grid5 = $('#rep_tab');

        if (grid5.data('loaded') !== true) {
            clickedstuff(id_to_position["rep_tab"]);
        }

        let exportData5 = grid5.jqxGrid('exportdata', 'csv');
        let downloadLink5;
        let dataType5 = 'text/csv';

        // Specify file name
        let filename5 = 'Replenishment.csv';

        // Create download link element
        downloadLink5 = document.createElement("a");
        document.body.appendChild(downloadLink5);

        // Create a link to the file
        downloadLink5.href = 'data:' + dataType5 + ', ' + exportData5;
        // Setting the file name
        downloadLink5.download = filename5;
        //triggering the function
        downloadLink5.click();

        // inv tab
        let grid6 = $('#inv_tab');

        if (grid6.data('loaded') !== true) {
            clickedstuff(id_to_position["inv_tab"]);
        }

        let exportData6 = grid6.jqxGrid('exportdata', 'csv');
        let downloadLink6;
        let dataType6 = 'text/csv';

        // Specify file name
        let filename6 = 'Inventory.csv';

        // Create download link element
        downloadLink6 = document.createElement("a");
        document.body.appendChild(downloadLink6);

        // Create a link to the file
        downloadLink6.href = 'data:' + dataType6 + ', ' + exportData6;
        // Setting the file name
        downloadLink6.download = filename6;
        //triggering the function
        downloadLink6.click();

        let grid8 = $('#po_tab');

        if (grid8.data('loaded') !== true) {
            clickedstuff(id_to_position["po_tab"]);
        }

        let exportData8 = grid8.jqxGrid('exportdata', 'csv');
        let downloadLink8;
        let dataType8 = 'text/csv';

        // Specify file name
        let filename8 = 'PO.csv';

        // Create download link element
        downloadLink8 = document.createElement("a");
        document.body.appendChild(downloadLink8);

        // Create a link to the file
        downloadLink8.href = 'data:' + dataType6 + ', ' + exportData6;
        // Setting the file name
        downloadLink8.download = filename8;
        //triggering the function
        downloadLink8.click();

        let grid9 = $('#rolling_7_sales');

        if (grid9.data('loaded') !== true) {
            clickedstuff(id_to_position["rolling_7_sales"]);
        }

        let exportData9 = grid.jqxGrid('exportdata', 'csv');
        let downloadLink9;
        let dataType9 = 'text/csv';

        // Specify file name
        let filename9 = 'Rolling_7_Sales.csv';

        // Create download link element
        downloadLink9 = document.createElement("a");
        document.body.appendChild(downloadLink);

        // Create a link to the file
        downloadLink9.href = 'data:' + dataType + ', ' + exportData;
        // Setting the file name
        downloadLink9.download = filename;
        //triggering the function
        downloadLink9.click();

    }


</script>
<style>
    html {
        height: 65%;
    }

    body {
        height: 100%;
    }

    .ui-dialog-titlebar-close {
        outline: none;
    }

    .ui-dialog-titlebar {
        /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#d3d3d3+0,c6c6c6+49,bfbfbf+51,969696+100 */
        background: rgb(211, 211, 211); /* Old browsers */
        background: -moz-linear-gradient(top, rgba(211, 211, 211, 1) 0%, rgba(198, 198, 198, 1) 49%, rgba(191, 191, 191, 1) 51%, rgba(150, 150, 150, 1) 100%); /* FF3.6-15 */
        background: -webkit-linear-gradient(top, rgba(211, 211, 211, 1) 0%, rgba(198, 198, 198, 1) 49%, rgba(191, 191, 191, 1) 51%, rgba(150, 150, 150, 1) 100%); /* Chrome10-25,Safari5.1-6 */
        background: linear-gradient(to bottom, rgba(211, 211, 211, 1) 0%, rgba(198, 198, 198, 1) 49%, rgba(191, 191, 191, 1) 51%, rgba(150, 150, 150, 1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
        /*filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#d3d3d3', endColorstr='#969696', GradientType=0); !* IE6-9 *!*/
    }

    #dialog_modal {
        display: none;
    }

    #page_modal {
        background: rgba(9, 9, 9, 0.65);
        width: 100%;
        height: 100%;
        z-index: 500;
        display: none;
        position: absolute;
        top: 0;
        left: 0;
    }

    .ui-dialog {
        z-index: 550;
    }

    div[role='columnheader'] {
        font-weight: bold;
        font-size: 15px;
        /*background: rgb(222, 244, 247) !important;*/
        text-align: center !important;
    }

    /*.jqx-grid-cell-alt-office {
        background: rgba(0, 103, 255, 0.07);
    }*/

    /*.jqx-grid-cell-pinned {
        background-color: rgba(2,26,43,0.89);
        color: whitesmoke !important;
    }*/

    /*.jqx-grid-cell-filter-row {
        background: rgb(133, 162, 165) !important;
    }*/

    #assignee_select {
        border: solid 1px #e4e4e1;
        z-index: 500;
        color: rgb(68, 68, 68);
        width: 95%;
        outline: none;
        position: relative;
        top: 5px;
        left: 5px;
    }

    #assignee_select_clone {
        display: none;
    }

    .jqx-grid-group-expand, .jqx-grid-group-expand-office {
        background-color: rgb(133, 162, 165) !important;
    }

    .jqx-grid-group-collapse, .jqx-grid-group-collapse-office {
        background-color: rgb(133, 162, 165) !important;
    }

    #sku_div {
        position: static;
        left: 40%;
        top: 5.5%;
        z-index: 6000;
        display: none;
    }

    th, td {
        padding: 10px;
    }

    #loading_spinner {
        display: none;
        position: absolute;
        bottom: -55px;
        left: 0px;
        width: 100%;
        height: 60%;
        background-image: url(https://media.tenor.com/On7kvXhzml4AAAAj/loading-gif.gif);
        background-position: center;
        background-repeat: no-repeat;
        background-size: 100px;
    }
</style>
</html>
