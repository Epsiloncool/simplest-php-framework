<?php

require dirname(__FILE__).'/header.php';

?><!-- Page Content -->
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row bg-title">
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                    <h4 class="page-title">Dashboard</h4> </div>
                <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> 
                    <?php echo breadcrumbs(); ?>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- .row -->
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="white-box analytics-info">
                        <h3 class="box-title">Total Visit</h3>
                        <ul class="list-inline two-part">
                            <li>
                                <div id="sparklinedash"></div>
                            </li>
                            <li class="text-right"><i class="ti-arrow-up text-success"></i> <span class="counter text-success">8659</span></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="white-box analytics-info">
                        <h3 class="box-title">Total Page Views</h3>
                        <ul class="list-inline two-part">
                            <li>
                                <div id="sparklinedash2"></div>
                            </li>
                            <li class="text-right"><i class="ti-arrow-up text-purple"></i> <span class="counter text-purple">7469</span></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="white-box analytics-info">
                        <h3 class="box-title">Unique Visitor</h3>
                        <ul class="list-inline two-part">
                            <li>
                                <div id="sparklinedash3"></div>
                            </li>
                            <li class="text-right"><i class="ti-arrow-up text-info"></i> <span class="counter text-info">6011</span></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="white-box analytics-info">
                        <h3 class="box-title">Bounce Rate</h3>
                        <ul class="list-inline two-part">
                            <li>
                                <div id="sparklinedash4"></div>
                            </li>
                            <li class="text-right"><i class="ti-arrow-down text-danger"></i> <span class="text-danger">18%</span></li>
                        </ul>
                    </div>
                </div>
            </div>
			<!--/.row -->
			<!--
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <h3 class="box-title">Sales in 2017</h3>
                                <p class="m-t-30">Lorem ipsum dolor sit amet, ectetur adipiscing elit. viverra tellus. ipsumdolorsitda amet, ectetur adipiscing elit.</p>
                                <p>
                                    <br/> Ectetur adipiscing elit. viverra tellus.ipsum dolor sit amet, dag adg ecteturadipiscingda elitdglj. vadghiverra tellus.</p>
                            </div>
                            <div class="col-md-8 col-sm-6 col-xs-12">
                                <div id="morris-area-chart" style="height:250px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            -->
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <h3 class="box-title">Current Tasks</h3>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>User</th>
                                        <th>Order date</th>
                                        <th>Amount</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Tracking Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><a href="javascript:void(0)" class="btn-link"> Order #53431</a></td>
                                        <td>Steve N. Horton</td>
                                        <td><span class="text-muted"><i class="fa fa-clock-o"></i> Oct 22, 2014</span></td>
                                        <td>$45.00</td>
                                        <td class="text-center">
                                            <div class="label label-table label-success">Paid</div>
                                        </td>
                                        <td class="text-center">-</td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0)" class="btn-link"> Order #53432</a></td>
                                        <td>Charles S Boyle</td>
                                        <td><span class="text-muted"><i class="fa fa-clock-o"></i> Oct 24, 2014</span></td>
                                        <td>$245.30</td>
                                        <td class="text-center">
                                            <div class="label label-table label-info">Shipped</div>
                                        </td>
                                        <td class="text-center"><i class="fa fa-plane"></i> CGX0089734531</td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0)" class="btn-link"> Order #53433</a></td>
                                        <td>Lucy Doe</td>
                                        <td><span class="text-muted"><i class="fa fa-clock-o"></i> Oct 24, 2014</span></td>
                                        <td>$38.00</td>
                                        <td class="text-center">
                                            <div class="label label-table label-info">Shipped</div>
                                        </td>
                                        <td class="text-center"><i class="fa fa-plane"></i> CGX0089934571</td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0)" class="btn-link"> Order #53434</a></td>
                                        <td>Teresa L. Doe</td>
                                        <td><span class="text-muted"><i class="fa fa-clock-o"></i> Oct 15, 2014</span></td>
                                        <td>$77.99</td>
                                        <td class="text-center">
                                            <div class="label label-table label-info">Shipped</div>
                                        </td>
                                        <td class="text-center"><i class="fa fa-plane"></i> CGX0089734574</td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0)" class="btn-link"> Order #53435</a></td>
                                        <td>Teresa L. Doe</td>
                                        <td><span class="text-muted"><i class="fa fa-clock-o"></i> Oct 12, 2014</span></td>
                                        <td>$18.00</td>
                                        <td class="text-center">
                                            <div class="label label-table label-success">Paid</div>
                                        </td>
                                        <td class="text-center">-</td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0)" class="btn-link">Order #53437</a></td>
                                        <td>Charles S Boyle</td>
                                        <td><span class="text-muted"><i class="fa fa-clock-o"></i> Oct 17, 2014</span></td>
                                        <td>$658.00</td>
                                        <td class="text-center">
                                            <div class="label label-table label-danger">Refunded</div>
                                        </td>
                                        <td class="text-center">-</td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0)" class="btn-link">Order #536584</a></td>
                                        <td>Scott S. Calabrese</td>
                                        <td><span class="text-muted"><i class="fa fa-clock-o"></i> Oct 19, 2014</span></td>
                                        <td>$45.58</td>
                                        <td class="text-center">
                                            <div class="label label-table label-warning">Unpaid</div>
                                        </td>
                                        <td class="text-center">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'php/right-sidebar.php';?>
        </div>
    </div>
<?php

require dirname(__FILE__).'/footer.php';