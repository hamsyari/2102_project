<!DOCTYPE html>
<html lang="en">
	<head>
    	<meta charset="utf-8">
    	<title>Dashboard</title>

	    <!-- Bootstrap core CSS -->
	    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
	  	<!-- Ionicons -->
	  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
	    <!-- daterange picker -->
	  	<link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
	    <!-- Custom styles for this template -->
	    <link href="../main.css" rel="stylesheet">
  </head>
  <body>
	<?php
		$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
	    or die('Could not connect: ' . pg_last_error());
	?>
	<div class="wrapper" style="height: auto;">
    	<header class="main-header">
		    <a href="index.php" class="logo logouser">
		      <span class="logo-lg"><b>CrowdFunder</b></span>
		    </a>
    		<nav class="navbar navbaruser navbar-static-top">
          		<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
	            	<span class="sr-only">Toggle navigation</span>
	          	</a>
	          	<div class="navbar-custom-menu">
	            	<ul class="nav navbar-nav">
	              		<li class="user user-menu">
	                		<a href="index.php">
	                  			<span class="hidden-xs">Profile</span>
	                		</a>
	              		</li>
	              		<li class="user user-menu">
                			<a href="projects.php">
	                  			<span class="hidden-xs">View Projects</span>
                			</a>
	              		</li>
	              		<li class="user user-menu">
	                		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
	                  			<span class="hidden-xs">Sign Out</span>
	                		</a>
	              		</li>
            		</ul>
          		</div>
        	</nav>
  		</header>
     	<div class="content-wrapper content-wrapper-user" style="min-height:916px;">
    		<!-- Main content -->
    		<section class="content">
      			<div class="row">
      				<div class="col-lg-2">
			            <div class="box project-box user-projects-nav">
		                	<div class="box-body">
		                		<h4>Navigation</h4>
			                    <ul class="list-group list-group-unbordered">
			                    	<?php
										$query = "SELECT COUNT(p.id) AS projcount FROM Project p
												  WHERE p.softdelete = false AND p.email = 'jwilliams1p@weibo.com'";
										$result = pg_query($query) or die('Query failed: ' . pg_last_error());
										$projectCount = pg_fetch_assoc($result);
									?>
		                     		<li class="list-group-item list-group-item-user active">
			                      		<a href="#myprojects" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-cube"></i> My Projects 
			                      		<?php 
			                      			if (!is_null($projectCount['projcount'])) { 
			                      				echo "<span class='badge badge-success'>".$projectCount['projcount']."</span>"; 
		                      				} else { 
		                      					echo "<span class='badge'>0</span>";
	                      					}?>
                      					</a>
			                    	</li>
			                    	<?php
										$query = "SELECT COUNT(p.id) AS projcount FROM Project p
												  WHERE p.softdelete = false";
										$result = pg_query($query) or die('Query failed: ' . pg_last_error());
										$projectCount = pg_fetch_assoc($result);
									?>
			                    	<li class="list-group-item list-group-item-user">
				                      	<a href="#allprojects" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-cubes"></i> All Projects
				                      	<?php 
			                      			if (!is_null($projectCount['projcount'])) { 
			                      				echo "<span class='badge badge-primary'>".$projectCount['projcount']."</span>"; 
		                      				} else { 
		                      					echo "<span class='badge'>0</span>";
	                      					}?>
                      					</a>
			                    	</li>
			                  	</ul>
		                	</div>
		              	</div>
		        	</div>
    				<div class="col-lg-10">
    					<div class="tab-content">
    						<div role="tabpanel" class="tab-pane active" id="myprojects">
    							<div class="box project-box">
		            				<div class="box-header">
		              					<h3 class="box-title">My Projects</h3>
		              					<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#projectForm" show="false"><span><i class="fa fa-plus"></i></span> New Project</button>
		              					<!-- Modal -->
										<div id="projectForm" class="modal fade" role="dialog">
					  						<div class="modal-dialog">
						
												<div class="modal-content">
													<form id="add-project-form" role="form" method="post">
													  	<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title">New Project</h4>
													  	</div>
													  	<div class="modal-body">
															<div class="input-group">
																<span class="input-group-addon">Title</span>
																<input name="title" type="text" class="form-control" placeholder="Enter Project Title">
															</div><br/>
															<div class="input-group">
																<span class="input-group-addon">Description</span>
																<textarea name="description" class="form-control custom-control" rows="3" style="resize:none" placeholder="Enter Project Description"></textarea>
															</div><br/>
															<div class="input-group">
															  <div class="input-group-addon">
																<i class="fa fa-calendar"></i>
															  </div>
															  <input name="duration" type="text" class="form-control pull-right" id="project-duration">
															</div><br/>
															<div class="input-group">
																<span class="input-group-addon"><i class="fa fa-dollar"></i></span>
																<input name="amount" type="number" min="100" max="1000000" class="form-control" placeholder="Goal Amount">
																<span class="input-group-addon">.00</span>
															</div><br/>
															<div class="input-group">
																<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
																<select name="category" class="form-control">
										 							<option value="" disabled selected>Select a category</option>
																 	<?php
																		$query = 'SELECT * FROM Category c';
																		$result = pg_query($query) or die('Query failed: ' . pg_last_error());
															 
																		while($row=pg_fetch_assoc($result)) {
																				echo "<option value='".$row['id']."'>".$row['name']."</option>";
																			}
																		
																		pg_free_result($result);
																	?>						
																</select>
															</div><br/>
						  								</div>
													  	<div class="modal-footer">
															<button type="submit" name="projectForm" class="btn btn-primary">Add Project</button>
															<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
												  		</div>
													</form>			
													<?php
													if(isset($_POST['projectForm'])){
														$dateStr = $_POST['duration'];
														$dateArr = (explode(" - ",$dateStr));
														$startDate = date('Y-m-d', strtotime(str_replace('/', '-', $dateArr[0])));
														$endDate = date('Y-m-d', strtotime(str_replace('/', '-', $dateArr[1])));
														
														$query = "INSERT INTO Project (title, description, startDate, endDate, categoryId, amountFundingSought, email)
																VALUES ('".$_POST['title']."','".$_POST['description']."','".$startDate."','".$endDate."','".$_POST['category']."',".$_POST['amount'].",'jwilliams1p@weibo.com')";
														
														$result = pg_query($query) or die('Query failed: ' . pg_last_error());
													}
												?>	
												</div>
					  						</div>
										</div>
					  					<br/>
		            				</div>
		            				<div class="box-body">
		            					<table id="myProjectsTable" class="table table-bordered table-hover table-striped" >
			                          		<thead>
				                            	<tr>
				                              		<th>Title</th>
				                              		<th>Start Date</th>
				                              		<th>End Date</th>
				                              		<th>Goal Amount</th>
				                              		<th>Category</th>
				                              		<th>Funding Received</th>
				                              		<th>No. of Donors</th>
				                              		<th>Status</th>
				                              		<th></th>
				                              		<th></th>
				                            	</tr>
				                          	</thead>
				                          	<tbody id="table_data">
					                          	<?php
					                            $query = "SELECT p.id, p.title, p.startdate, p.enddate, p.amountfundingsought, p.softdelete, c.name, b.sum, b.donors
					                                      FROM Project p INNER JOIN Member m ON p.email = m.email
					                                                     LEFT OUTER JOIN (SELECT t.projectId, COUNT(DISTINCT t.email) AS Donors, SUM(t.amount) AS SUM
					                                                                      FROM Trans t
					                                                                      GROUP BY t.projectId) b ON b.projectId = p.id
					                                                    INNER JOIN category c ON c.id = p.categoryId
					                                      WHERE p.email = 'jwilliams1p@weibo.com' AND p.softDelete = false
					                                      ORDER BY p.enddate DESC, p.startdate DESC";

					                            $result = pg_query($query) or die('Query failed: ' . pg_last_error());
					                         
					                            if (pg_num_rows($result) > 0) {
					                              	while($row=pg_fetch_assoc($result)) {
					                              		if ((!is_null($row['sum'])) && ($row['sum'] >= $row['amountfundingsought'])) { 
															echo "<tr style=\"background-color:#c9ffc9;\">";
														} else {
															echo "<tr>";
														}
						                                echo "<td>".$row['title']."</td>
						                                      <td>".date('d/m/Y', strtotime(str_replace('-', '/', $row['startdate'])))."</td>
						                                      <td>".date('d/m/Y', strtotime(str_replace('-', '/', $row['enddate'])))."</td>
						                                      <td>$".$row['amountfundingsought']."</td>
						                                      <td>".$row['name']."</td>";
						                                
						                                if ((!is_null($row['sum'])) && ($row['sum'] >= $row['amountfundingsought'])) {
															echo "<td><strong style=\"color:#5cb85c;\">$".$row['sum']."</strong></td>";
						                                } else if ((!is_null($row['sum']))) {
						                                	echo "<td>$".$row['sum']."</td>";
														} else {
					                                  		echo "<td>$0</td>";
						                                }

						                                if (!is_null($row['donors'])) {
						                                  echo "<td>".$row['donors']."</td>";
						                                } else {
						                                  echo "<td>0</td>";
						                                }
						                                
						                                if (new DateTime() > new DateTime($row['enddate'])) {
						                                  echo "<td><span class='label label-danger'>Past</span></td>";
						                                } else if ((!is_null($row['sum'])) && ($row['sum'] >= $row['amountfundingsought'])) {
						                                  echo "<td><span class='label label-success'>Funded</span></td>";
						                                } else {
						                                  echo "<td><span class='label label-success'>Ongoing</span></td>";
						                                }
						                                $proj_id = $row['id'];

														echo "</td>
															<td><button class=\"btn btn-primary btn-xs\" onClick=\"location.href='project.php?id=$proj_id'\"><span class=\"glyphicon glyphicon-info-sign\"></span></button></td>
															<td><button class=\"btn btn-danger btn-xs delete_project\" project-id=\"$proj_id\" href=\"javascript:void(0)\"><span class=\"glyphicon glyphicon-trash\"></span></button></td>
															</tr>";
						                              }
						                            } else {
						                              echo "<td colspan=9 class\"text-center\">You have not created any project.</td>";
						                            }
					                          	?>
				                          	</tbody>
				                        </table>
		            				</div>
	            				</div>
		          			</div>
	          				<div role="tabpanel" class="tab-pane" id="allprojects">
	          					<div class="box project-box">
		            				<div class="box-header">
		              					<h3 class="box-title">All Projects</h3>
					  					<br/>
		            				</div>
		            				<div class="box-body">
										<table id="projectsTable" class="table table-bordered table-hover" >
							                <thead>
												<tr>
													<th>Title</th>
													<th>Start Date</th>
													<th>End Date</th>
													<th>Category</th>
													<th>Amount Raised</th>
													<th></th>
												</tr>
							                </thead>
							                <tbody id="table_data">
								                <?php
													$query = 'SELECT p.id, p.title, p.startDate, p.endDate, c.name, p.amountFundingSought, p.email, b.sum
															FROM Project p LEFT OUTER JOIN (SELECT t.projectId, SUM(t.amount) AS SUM 
																						FROM Trans t
																						GROUP BY t.projectId) b ON b.projectId = p.id 
																						, Category c
															WHERE c.id = p.categoryId AND p.softDelete = FALSE AND P.endDate >= current_date
															ORDER BY p.endDate DESC, p.startDate DESC';
													$result = pg_query($query) or die('Query failed: ' . pg_last_error());
								         
													while($row=pg_fetch_assoc($result)) {
															if ((!is_null($row['sum'])) && ($row['sum'] >= $row['amountfundingsought'])) { 
																echo "<tr style=\"background-color:#c9ffc9;\">";
															} else {
																echo "<tr>";
															}
															
															echo "<td>".$row['title']
															."</td><td>".date('d/m/Y', strtotime(str_replace('-', '/', $row['startdate'])))
															."</td><td>".date('d/m/Y', strtotime(str_replace('-', '/', $row['enddate'])))
															."</td><td>".$row['name']
															."</td><td><div class=\"progress\" style=\"margin-bottom:2px;\"><div class=\"progress-bar progress-bar-success\" role=\"progressbar\" aria-valuenow=\"70\"
															aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width:"
															.(($row['sum'] / $row['amountfundingsought'])*100)
															."%;\">
															</div></div>"; 
															
															if (is_null($row['sum'])) {
																echo "$0 / $".$row['amountfundingsought'];
															}else if ($row['sum'] >= $row['amountfundingsought']) {
																echo " <strong style=\"color:#5cb85c;\">$".$row['sum']."</strong> / $".$row['amountfundingsought'];
															} else {
																echo "$".$row['sum']." / $".$row['amountfundingsought'];
															} 
										                    $proj_id = $row['id'];

															echo "</td>
																<td><button class=\"btn btn-primary btn-xs\" onClick=\"location.href='project.php?id=$proj_id'\"><span class=\"glyphicon glyphicon-info-sign\"></span></button></td></tr>";
														}
													
													pg_free_result($result);
													
												?>
		                					</tbody>
		              					</table>
		            				</div>
		          				</div>
	          				</div>
          				</div>
        			</div>
      			</div>
    		</section>
  		</div>
	</div>

	<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
	<script src="../plugins/daterangepicker/daterangepicker.js"></script>
	<script src="../bootstrap/js/bootstrap.min.js"></script>
	<script src="../plugins/bootbox.min.js"></script>

  	<script>
    	$(document).ready(function(){
	        $('.delete_project').click(function(e){
          		e.preventDefault();
	          	var pid = $(this).attr('project-id');
	          	var parent = $(this).parent("td").parent("tr");
	          	bootbox.dialog({
	            	message: "Are you sure you want to delete this project?",
	            	title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
	            	buttons: {
		            	danger: {
			              	label: "Delete!",
			              	className: "btn-danger",
			              	callback: function() {

		                		$.post('../deletion/delete_project.php', { 'delete':pid })
				                .done(function(response){
				                  bootbox.alert(response);
				                  parent.fadeOut('slow');
				                })
				                .fail(function(){
				                  bootbox.alert('Something Went Wrong ....');
				                  })                            
			                }
		              	},
			            success: {
			              label: "No",
			              className: "btn-success",
			              callback: function() {
			               $('.bootbox').modal('hide');
			                }
		             	}	 
            		}
      			});
    		});
		});
	</script>
	<script>
		$(function() {
			var startDate;
			var endDate;
			
			$('#project-duration').daterangepicker({
				"minDate": new Date(),
				"locale": {
					"format": "DD/MM/YYYY",
				}
			});
		});
	</script>
  </body>
</html>
