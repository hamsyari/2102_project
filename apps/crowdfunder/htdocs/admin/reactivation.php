
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>CrowdFunder</title>

    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../plugins/font-awesome.min.css">
    <link href="../main.css" rel="stylesheet">


  </head>

  <body>
    <?php
    session_start();
    if (!isset($_SESSION['usr_id'])) {
      header("Location: ../login.php");
    } else if ($_SESSION['usr_role'] == 2) {
      header("Location: ../user/index.php");
    }

  	$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
      or die('Could not connect: ' . pg_last_error());

      $query = "SELECT m.firstname, m.lastname
              FROM member m 
              WHERE m.email = '".$_SESSION['usr_id']."'";
      $result = pg_query($query) or die('Query failed: ' . pg_last_error());
      $user=pg_fetch_assoc($result);
  	?>
  	<div class="wrapper" style="height: auto;">



      <header class="main-header">

      <!-- Logo -->
      <a href="index.php" class="logo">
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>CrowdFunder</b>Admin</span>
      </a>

      <!-- Header Navbar: style can be found in header.less -->
      <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
          <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $user['firstname']." ".$user['lastname'];?><span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="../user/index.php">Switch to user</a></li>
                <li><a href="../logout.php">Sign Out</a></li>
              </ul>
          </li>
          </ul>
        </div>
      </nav>
    </header>
<!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar" style="height:auto;">
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">NAVIGATION</li>
        <li class="treeview">
          <a href="index.php">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li>
		<li class="treeview">
          <a href="users.php">
            <i class="fa fa-users"></i> <span>Users</span>
          </a>
        </li>
		<li class="treeview">
          <a href="projects.php">
            <i class="fa fa-lightbulb-o"></i> <span>Projects</span>
          </a>
        </li>
		<li class="treeview">
          <a href="funding.php">
            <i class="fa fa-dollar"></i> <span>Funding</span>
          </a>
        </li>
		<li class="treeview">
          <a href="categories.php">
            <i class="fa fa-gear"></i> <span>Category</span>
          </a>
        </li>
        <li class="treeview">
          <a href="analytics.php">
            <i class="fa fa-bar-chart"></i> <span>Analytics</span>
          </a>
        </li>
		<li class="active treeview">
          <a href="reactivation.php">
            <i class="fa fa-recycle"></i> <span>Reactivation</span>
          </a>
        </li>
        <li clas="treeview">
          <a href="history.php">
            <i class="fa fa-history"></i><span>History</span>
          </a>
        </li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
     <div class="content-wrapper" style="min-height:916px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Reactivation of Deleted Items
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box reactivation-box">
            <div class="box-header">
              <h3 class="box-title" id="user-title">Deleted Users</h3>
              <h3 class="box-title" id="project-title">Deleted Projects</h3>
              <h3 class="box-title" id="funding-title">Deleted Fundings</h3>
              <h3 class="box-title" id="category-title">Deleted Categories</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
			<div class="row">
				<div class="col-md-2">
					<select name="search-item" class="form-control" onChange="getState(this.value);">
						<option value="users">Deleted Users</option>
						<option value="projects">Deleted Projects</option>
						<option value="fundings">Deleted Fundings</option>
						<option value="categories">Deleted Categories</option>
					</select>
				</div>
			</div>

			<br/>
			<table id="usersTable" class="table table-bordered table-hover" >
                <thead>
					<tr>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Email</th>
						<th>Country</th>
						<th>Registration Date</th>
						<th>Role Type</th>
						<th>Projects Created</th>
						<th>Projects Funded</th>
						<th>Total Donation</th>
						<th></th>
						<th></th>
					</tr>
                </thead>
                <tbody>
                <?php
					$query = 'SELECT m.firstName, m.lastName, m.email, c.name AS country_name, m.registrationDate, r.type, COUNT(p.id) AS proj_created, COUNT(DISTINCT t.projectId) AS proj_funded, SUM(t.amount) AS donation
								FROM Member m LEFT OUTER JOIN Project p ON m.email = p.email
											 LEFT OUTER JOIN Trans t ON m.email = t.email
											 LEFT OUTER JOIN (SELECT t.email, SUM(t.amount) FROM Trans t GROUP BY t.email) b ON b.email = m.email,
								Country c, Role r
								WHERE m.countryId = c.id AND r.id = m.roleId AND m.softDelete = TRUE
								GROUP BY m.firstName, m.lastName, m.email, c.name, m.registrationDate, r.type
								ORDER BY m.firstName, m.lastName';
					$result = pg_query($query) or die('Query failed: ' . pg_last_error());

					while($row=pg_fetch_assoc($result)) {
							echo "<tr><td>".$row['firstname']
							."</td><td>".$row['lastname']
							."</td><td>".$row['email']
							."</td><td>".$row['country_name']
							."</td><td>".$row['registrationdate']
							."</td><td>".$row['type'] //TODO: Add privilege level here
							."</td><td>".$row['proj_created']
							."</td><td>".$row['proj_funded']."</td>";

							if($row['donation'] != 0) {
								echo "<td>$".$row['donation']."</td>";
							} else {
								echo "<td>$0</td>";
							}
							$user_email = $row['email'];

							echo "<td><button class=\"btn btn-primary btn-xs\" onClick=\"location.href='user_details.php?email=$user_email'\"><span class=\"glyphicon glyphicon-info-sign\"></span></button></td>
							<td><button class=\"btn btn-danger btn-xs delete_user\" user-email=\"$user_email\" href=\"javascript:void(0)\"><span class=\"glyphicon glyphicon-trash\"></span></button></td></tr>";
						}

					pg_free_result($result);
				?>
                </tbody>
              </table>

              <table id="projectsTable" class="table table-bordered table-hover" >
                <thead>
					<tr>
						<th>Title</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Category</th>
						<th>Amount Raised</th>
						<th>Organiser</th>
						<th></th>
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
							WHERE c.id = p.categoryId AND p.softDelete = TRUE
							ORDER BY p.endDate DESC, p.startDate DESC';
					$result = pg_query($query) or die('Query failed: ' . pg_last_error());

					while($row=pg_fetch_assoc($result)) {
							if ((!is_null($row['sum'])) && ($row['sum'] >= $row['amountfundingsought'])) {
								echo "<tr style=\"background-color:#c9ffc9;\">";
							} else {
								echo "<tr>";
							}

							echo "<td>".$row['title']
							."</td><td>".$row['startdate']
							."</td><td>".$row['enddate']
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

							echo "</td><td>".$row['email'].
							"</td><td><button class=\"btn btn-primary btn-xs\" onClick=\"location.href='project_details.php?id=$proj_id'\"><span class=\"glyphicon glyphicon-info-sign\"></span></button></td>
							<td><button class=\"btn btn-danger btn-xs delete_project\" project-id=\"$proj_id\" href=\"javascript:void(0)\"><span class=\"glyphicon glyphicon-trash\"></span></button></td></tr>";
						}

					pg_free_result($result);

				?>
                </tbody>
              </table>

              <table id="fundingsTable" class="table table-bordered table-hover" >
              	<thead>
                <tr>
                  <th>Amount</th>
                  <th>Date</th>
                  <th>Project Name</th>
                  <th>Donor Email</th>
                  <th></th>
                  <th></th>
                </tr>
                      </thead>
                      <tbody id="table_data">
                      <?php

                $query = 'SELECT t.amount, t.date, p.title, t.email, t.transactionNo
                            FROM Trans t, Project p
                            WHERE t.projectId = p.id AND t.softDelete = TRUE
                            ORDER BY t.date DESC';
                $result = pg_query($query) or die('Query failed: ' . pg_last_error());

                while($row=pg_fetch_assoc($result)) {
                    $trans_no = $row['transactionno'];
                    echo "<tr><td>$".$row['amount'].
                    "</td><td>".$row['date'].
                    "</td><td>".$row['title'].
                    "</td><td>".$row['email']."</td>";

                    echo "<td><button class=\"btn btn-primary btn-xs\" onClick=\"location.href='funding_details.php?trans-no=$trans_no'\"><span class=\"glyphicon glyphicon-info-sign\"></span></button></td>
                    <td><button class=\"btn btn-danger btn-xs delete_funding\" funding-id=\"$trans_no\" href=\"javascript:void(0)\"><span class=\"glyphicon glyphicon-trash\"></span></button></td></tr>";

                  }

                pg_free_result($result);
              ?>
              </tbody>
            </table>

			<table id="categoryTable" class="table table-bordered table-hover" >
                <thead>
					<tr>
						<th>Name</th>
						<th>Associated Projects</th>
						<th>No. of Donors</th>
						<th>Funding Achieved</th>
						<th>Status</th>
						<th></th>
						<th></th>
					</tr>
                </thead>
                <tbody id="table_data">
	                <?php
						$query = 'SELECT *
									FROM Category c LEFT OUTER JOIN (SELECT p.categoryid, donors, total, COUNT(p.categoryid) AS pcount
															FROM Project p LEFT OUTER JOIN (SELECT p2.categoryid, COUNT(DISTINCT t.email) AS donors, SUM(t.amount) AS total
							                            FROM Project p2 INNER JOIN Trans t ON p2.id = t.projectId
							                            GROUP BY p2.categoryid) pTrans
							                            ON p.categoryid = pTrans.categoryId
													GROUP BY p.categoryid, total, donors) fundedCategories
	    											ON c.id = fundedCategories.categoryid
	    							WHERE c.softdelete = TRUE
	    							ORDER BY c.name';
						$result = pg_query($query) or die('Query failed: ' . pg_last_error());

						while($category=pg_fetch_assoc($result)) {
		                    $categoryId = $category['id'];
							echo "<td>".$category['name']."</td>";
							if ($category['pcount'] != 0) {
					  			echo "<td>".$category['pcount']."</td>";
					  		} else {
					  			echo "<td>0</td>";
					  		}
					  		if ($category['donors'] != 0) {
					  			echo "<td>".$category['donors']."</td>";
					  		} else {
					  			echo "<td>0</td>";
					  		}
					  		if ($category['total'] != 0) {
					  			echo "<td>$".$category['total']."</td>";
					  		} else {
					  			echo "<td>$0</td>";
					  		}
				  			echo "<td><span class='label label-danger'>Inactive</span></td>";

					  		echo "<td>
					  				<button class=\"btn btn-primary btn-xs\" onClick=\"location.href='category_details.php?id=$categoryId'\">
					  					<span class=\"glyphicon glyphicon-info-sign\"></span>
				  					</button>
				  				  </td>";
				  			echo "<td>
				  					<button class=\"btn btn-success btn-xs reactivate_category\" category-id=\"$categoryId\" href=\"javascript:void(0)\">
				  					<span class=\"glyphicon glyphicon-share-alt\"></span></button>
			  					  </td>
			  					  </tr>";

						}
						pg_free_result($result);
					?>
    			</tbody>
  			</table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
	</div>
	<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
	<script src="../plugins/bootbox.min.js"></script>

  	<script>
	    $(document).ready(function(){
	    	$("#user-title").show();
	    	$("#project-title").hide();
	    	$("#funding-title").hide();
	    	$("#category-title").hide();

         	$("#usersTable").show();
     		$("#projectsTable").hide();
     		$("#fundingsTable").hide();
     		$("#categoryTable").hide();

	        $('.reactivate_user').click(function(e){

	          e.preventDefault();

	          var pid = $(this).attr('user-email');
	          console.log(pid);
	          var parent = $(this).parent("td").parent("tr");
	          bootbox.dialog({
	            message: "Are you sure you want to reactivate this user?",
	            title: "<i class='glyphicon glyphicon-share-alt'></i>   Reativate !",
	            buttons: {
	            success: {
	              label: "Reactivate!",
	              className: "btn-success",
	              callback: function() {

	                $.post('reactivate/reactivate_user.php', { 'delete':pid })
	                .done(function(response){
	                  bootbox.alert(response);
	                  parent.fadeOut('slow');
	                })
	                .fail(function(){
	                  bootbox.alert('Something Went Wrong ....');
	                  })
	                }
	              },
	            primary: {
	              label: "No",
	              className: "btn-primary",
	              callback: function() {
	               $('.bootbox').modal('hide');
	                }
	             }

	            }
	          });


	        });
	        $('.reactivate_project').click(function(e){

	          e.preventDefault();

	          var pid = $(this).attr('project-id');
	          var parent = $(this).parent("td").parent("tr");
	          bootbox.dialog({
	            message: "Are you sure you want to reactivate this project?",
	            title: "<i class='glyphicon glyphicon-share-alt'></i>   Reativate !",
	            buttons: {
	            success: {
	              label: "Reactivate!",
	              className: "btn-success",
	              callback: function() {

	                $.post('reactivate/reactivate_project.php', { 'delete':pid })
	                .done(function(response){
	                  bootbox.alert(response);
	                  parent.fadeOut('slow');
	                })
	                .fail(function(){
	                  bootbox.alert('Something Went Wrong ....');
	                  })
	                }
	              },
	            primary: {
	              label: "No",
	              className: "btn-primary",
	              callback: function() {
	               $('.bootbox').modal('hide');
	                }
	             }

	            }
	          });


	        });
	        $('.reactivate_funding').click(function(e){

	          e.preventDefault();

	          var pid = $(this).attr('funding-id');
	          var parent = $(this).parent("td").parent("tr");
	          bootbox.dialog({
	            message: "Are you sure you want to reactivate this funding?",
	            title: "<i class='glyphicon glyphicon-share-alt'></i>   Reativate !",
	            buttons: {
	            success: {
	              label: "Reactivate!",
	              className: "btn-success",
	              callback: function() {

	                $.post('reactivate/reactivate_funding.php', { 'delete':pid })
	                .done(function(response){
	                  bootbox.alert(response);
	                  parent.fadeOut('slow');
	                })
	                .fail(function(){
	                  bootbox.alert('Something Went Wrong ....');
	                  })
	                }
	              },
	            primary: {
	              label: "No",
	              className: "btn-primary",
	              callback: function() {
	               $('.bootbox').modal('hide');
	                }
	             }

	            }
	          });


	        });

	        $('.reactivate_category').click(function(e){

	          e.preventDefault();

	          var pid = $(this).attr('category-id');
	          var parent = $(this).parent("td").parent("tr");
	          bootbox.dialog({
	            message: "Are you sure you want to reactivate this category?",
	            title: "<i class='glyphicon glyphicon-share-alt'></i>   Reativate !",
	            buttons: {
	            success: {
	              label: "Reactivate!",
	              className: "btn-success",
	              callback: function() {

	                $.post('reactivate/reactivate_category.php', { 'delete':pid })
	                .done(function(response){
	                  bootbox.alert(response);
	                  parent.fadeOut('slow');
	                })
	                .fail(function(){
	                  bootbox.alert('Something Went Wrong ....');
	                  })
	                }
	              },
	            primary: {
	              label: "No",
	              className: "btn-primary",
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
		function getState(val) {
			if(val == "users") {
				$("#user-title").show();
	    		$("#project-title").hide();
	    		$("#funding-title").hide();
	    		$("#category-title").hide();

				$("#usersTable").show();
	     		$("#projectsTable").hide();
	     		$("#fundingsTable").hide();
	     		$("#categoryTable").hide();

			} else if(val == "projects") {
				$("#user-title").hide();
	    		$("#project-title").show();
	    		$("#funding-title").hide();
	    		$("#category-title").hide();

				$("#usersTable").hide();
	     		$("#projectsTable").show();
	     		$("#fundingsTable").hide();
     			$("#categoryTable").hide();

			} else if(val == "fundings") {
				$("#user-title").hide();
	    		$("#project-title").hide();
	    		$("#funding-title").show();
	    		$("#category-title").hide();

				$("#usersTable").hide();
	     		$("#projectsTable").hide();
	     		$("#fundingsTable").show();
	     		$("#categoryTable").hide();
			} else if(val == "categories") {
				$("#user-title").hide();
	    		$("#project-title").hide();
	    		$("#funding-title").hide();
	    		$("#category-title").show();

				$("#usersTable").hide();
	     		$("#projectsTable").hide();
	     		$("#fundingsTable").hide();
	     		$("#categoryTable").show();
			}
		}
	</script>
  </body>
</html>
