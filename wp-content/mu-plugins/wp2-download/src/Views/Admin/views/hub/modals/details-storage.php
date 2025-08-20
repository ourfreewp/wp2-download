<div class="modal fade" id="r2DetailsModal" tabindex="-1" aria-labelledby="r2DetailsModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="r2DetailsModalLabel">R2 Artifact Details</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div id="modal-content-body">
					<h6 class="fw-semibold mt-2">Releases in R2 Bucket: my-wp2-packages</h6>
					<div class="table-responsive">
						<table class="table table-sm table-striped">
							<thead>
								<tr>
									<th>Version</th>
									<th>Artifact</th>
									<th>Date</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>v1.0.0</td>
									<td><span class="code">wp2-download-v1.0.0.zip</span></td>
									<td>8/1/2025, 12:00:00 PM</td>
								</tr>
								<tr>
									<td>v1.1.0</td>
									<td><span class="code">wp2-download-v1.1.0.zip</span></td>
									<td>8/5/2025, 2:30:00 PM</td>
								</tr>
								<tr>
									<td>v1.1.1</td>
									<td><span class="code">wp2-download-v1.1.1.zip</span></td>
									<td>8/10/2025, 9:15:00 AM</td>
								</tr>
							</tbody>
						</table>
					</div>
					<h6 class="fw-semibold mt-4 text-danger">Missing Artifacts</h6>
					<div class="table-responsive">
						<table class="table table-sm table-striped">
							<thead>
								<tr>
									<th>Version</th>
									<th>Error</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>v1.0.1</td>
									<td><span class="text-danger">Artifact not found in bucket</span></td>
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