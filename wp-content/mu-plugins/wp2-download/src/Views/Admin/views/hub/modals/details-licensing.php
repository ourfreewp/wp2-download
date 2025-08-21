<?php
/**
 * Summary of Keygen Licensing Details Modal
 */

defined( 'ABSPATH' ) || exit();
?>

<div class="modal fade" id="keygenDetailsModal" tabindex="-1" aria-labelledby="keygenDetailsModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="keygenDetailsModalLabel">Licensing Details</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div id="modal-content-body">
					<h6 class="fw-semibold mt-2">Licensing Summary</h6>
					<p><strong>Keygen Account ID:</strong> your-keygen-account-id</p>
					<p><strong>API Status:</strong> OK</p>
					<hr>
					<h6 class="fw-semibold mt-4">Managed Licenses</h6>
					<div class="table-responsive">
						<table class="table table-sm table-striped">
							<thead>
								<tr>
									<th>License Key</th>
									<th>Status</th>
									<th>Activations</th>
									<th>Created</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><span class="code">xxxx-xxxx-xxxx-xxxx</span></td>
									<td><span class="badge rounded-pill text-bg-success">ACTIVE</span></td>
									<td>3 / 5</td>
									<td>2023-01-15</td>
								</tr>
								<tr>
									<td><span class="code">yyyy-yyyy-yyyy-yyyy</span></td>
									<td><span class="badge rounded-pill text-bg-warning">SUSPENDED</span></td>
									<td>1 / 1</td>
									<td>2022-11-01</td>
								</tr>
							</tbody>
						</table>
					</div>
					<h6 class="fw-semibold mt-4">Managed Policies</h6>
					<div class="table-responsive">
						<table class="table table-sm table-striped">
							<thead>
								<tr>
									<th>Policy Name</th>
									<th>ID</th>
									<th>Max Activations</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Standard Plugin License</td>
									<td>12345-policy</td>
									<td>5</td>
								</tr>
								<tr>
									<td>Unlimited License</td>
									<td>67890-policy</td>
									<td>999</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
