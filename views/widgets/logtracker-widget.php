<?php
$log_data = getLogData();
$chartData['labels'] = ["ERROR", "DEBUG", "INFO"];
$chartData['datasets'] = [
    [
        "data" => [$log_data['data']['ERROR']['count'] ?? 0, $log_data['data']['DEBUG']['count'] ?? 0, $log_data['data']['INFO']['count'] ?? 0],
        "backgroundColor" => ["#FF5722", "#90CAF9", "#1976D2"],
    ]
];
$chartData = json_encode($chartData);
?>

<div class="widget" id="widget-state"
     data-name="<?php echo _l('logtracker_stats'); ?>">
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body padding-10">
                    <div class="widget-dragger ui-sortable-handle"></div>
                    <p
                            class="tw-font-medium tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse tw-p-1.5">
						<span class="tw-text-neutral-700">
							<?php echo _l('logtracker_stats'); ?>
						</span>
                    </p>
                    <hr class="-tw-mx-3 tw-mt-3 tw-mb-6">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="relative" style="height:250px">
                                <canvas class="chart" height="250" id="log_tracker_chart"
                                        data-json='<?= $chartData ?>'></canvas>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-box level level-all ">
										<span class="info-box-icon">
											<i class="fa-solid fa-list"></i>
										</span>
                                        <div class="info-box-content ">
											<span class="info-box-text">
												<?php echo _l('all') ?>
											</span>

                                            <br/>
                                            <span class="info-box-number">
												<?php echo $log_data['count'] ?? 0 ?>
                                                <?php echo _l('entries') ?>
											</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="info-box level level-error">
										<span class="info-box-icon ">
											<i class="fa-solid fa-circle-xmark"></i>
										</span>
                                        <div class="info-box-content ">
											<span class="info-box-text">
												<?php echo _l('error') ?>
											</span>

                                            <br/>
                                            <span class="info-box-number">
												<?php echo $log_data['data']['ERROR']['count'] ?? 0 ?>
                                                <?php echo _l('entries') ?>
											</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4  ">
                                    <div class="info-box level level-info">
										<span class="info-box-icon">
											<i class="fa-solid fa-circle-info"></i>
										</span>
                                        <div class="info-box-content ">
											<span class="info-box-text">
												<?php echo _l('info') ?>
											</span>

                                            <br/>
                                            <span class="info-box-number">
												<?php echo $log_data['data']['INFO']['count'] ?? 0 ?>
                                                <?php echo _l('entries') ?>
											</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4  ">
                                    <div class="info-box level level-debug">
										<span class="info-box-icon ">
											<i class="fa-solid fa-circle-radiation"></i>
										</span>
                                        <div class="info-box-content ">
											<span class="info-box-text">
												<?php echo _l('debug') ?>
											</span>

                                            <br/>
                                            <span class="info-box-number">
												<?php echo $log_data['data']['DEBUG']['count'] ?? 0 ?>
                                                <?php echo _l('entries') ?>
											</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>