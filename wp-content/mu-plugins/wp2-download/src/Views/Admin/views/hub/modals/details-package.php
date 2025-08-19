<!-- Package Details Modal -->
<div class="modal fade" id="packageDetailsModal" tabindex="-1" aria-labelledby="packageDetailsModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="packageDetailsModalLabel"></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row g-4">
					<div class="col-lg-6">
						<div class="card h-100">
							<div class="card-body">
								<h6 class="card-subtitle mb-2 text-muted">Summary</h6>
								<p class="mb-1"><strong>Name:</strong> <span id="detailsName"></span></p>
								<p class="mb-1"><strong>Type:</strong> <span id="detailsType"></span></p>
								<p class="mb-1"><strong>Slug:</strong> <span id="detailsSlug"></span></p>
								<p class="mb-1"><strong>Latest Version:</strong> <span id="detailsVersion"></span></p>
								<p class="mb-1"><strong>Last Tag:</strong> <span id="detailsLastTag"></span></p>
								<p class="mb-1"><strong>Health:</strong> <span id="detailsHealth"></span></p>
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="card h-100">
							<div class="card-body">
								<h6 class="card-subtitle mb-2 text-muted">API Endpoints</h6>
								<ul class="list-group list-group-flush">
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span>Check for Latest Version:</span>
										<code class="text-secondary small" id="endpointLatest"></code>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span>Download a Specific Version:</span>
										<code class="text-secondary small" id="endpointDownload"></code>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span>Trigger Health Check:</span>
										<code class="text-secondary small" id="endpointHealthCheck"></code>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>

				<h6 class="mt-4 mb-3">Release History</h6>
				<div class="table-responsive">
					<table class="table table-striped table-hover mb-0">
						<thead>
							<tr>
								<th scope="col">Version</th>
								<th scope="col">Date Registered</th>
								<th scope="col">R2 Artifact Status</th>
							</tr>
						</thead>
						<tbody id="releaseHistoryTableBody"></tbody>
					</table>
				</div>

				<h6 class="mt-4 mb-3">Active Sites</h6>
				<div class="table-responsive">
					<table class="table table-striped table-hover mb-0">
						<thead>
							<tr>
								<th scope="col">Site URL</th>
								<th scope="col">Version</th>
								<th scope="col">Last Reported</th>
							</tr>
						</thead>
						<tbody id="activeSitesTableBody"></tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>