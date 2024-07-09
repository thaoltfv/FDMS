<template>
	<b-overlay :show="showLoadingPrint" rounded="sm">
		<div
			class="detail_pre_certification row"
			:style="isMobile ? { margin: '0' } : {}"
		>
			<div class="col-12" :style="isMobile ? { padding: '0' } : {}">
				<div class="card">
					<div class="card-title">
						<div class="d-flex justify-content-between align-items-center">
							<h3 class="title">
								{{ isMobile ? "Thông tin" : "Thông tin chung" }}
							</h3>
							<div class="row" style="display: flex; align-items: center">
								<div class="color_content card-status-pre-certificate">
									{{ dataPC.id ? `YCSB_${dataPC.id}` : "YCSB" }} |
									<span>{{ statusDescription }}</span>
								</div>
								<!-- <a-dropdown v-if="showExportDocument && dataPC.status !== 1">
								<a-button class="btn-export">
									<a-icon type="download" />
								</a-button>
								<template #overlay>
									<a-menu @click="handleMenuClick">
										<a-menu-item key="1"> Giấy yêu cầu TĐG </a-menu-item>
										<a-menu-item key="2"> Hợp đồng TĐG </a-menu-item>
										<a-menu-item key="3"> Kế hoạch TĐG </a-menu-item>
										<a-menu-item key="4"> Biên bản thanh lý </a-menu-item>
									</a-menu>
								</template>
							</a-dropdown> -->
								<div
									v-if="dataPC.certificate_id"
									@click="handleDetailCertificate(dataPC.certificate_id)"
									id="certificate_id"
									class="ml-3 mr-4 arrowBox arrow-right"
								>
									<icon-base
										name="nav_hstd_2"
										width="20px"
										height="20px"
										class="item-icon svg-inline--fa"
									/>
									{{ `HTSD_${dataPC.certificate_id}` }}
									<b-tooltip target="certificate_id" placement="top-right">{{
										`Đã chuyển chính thức HTSD_${dataPC.certificate_id}`
									}}</b-tooltip>
								</div>
							</div>
						</div>
					</div>
					<div v-if="!isMobile" class="card-body card-info">
						<div class="row justify-content-between">
							<div class="col-md-12 col-lg-6 mt-1 d-grid h-100">
								<div class="detail_certificate_1 h-100">
									<div class="d-flex container_content justify-content-between">
										<div class="d-flex container_content">
											<strong class="margin_content_inline">Khách hàng:</strong>
											<p>{{ dataPC.petitioner_name }}</p>
										</div>
										<div
											v-if="editInfo && edit"
											@click="handleShowAppraiseInformation"
											class="btn-edit"
										>
											<img src="@/assets/icons/ic_edit_3.svg" alt="add" />
										</div>
									</div>
									<div class="row d-flex container_content">
										<strong class="margin_content_inline"
											>MST/CMND/CCCD/Passport:</strong
										>
										<p>{{ dataPC.petitioner_identity_card }}</p>
										<strong class="margin_content_inline">Điện thoại:</strong>
										<p>{{ dataPC.petitioner_phone }}</p>
									</div>
									<!-- <div class="d-flex container_content">
											<strong class="margin_content_inline">Điện thoại:</strong> <p>{{dataPC.petitioner_phone}}</p>
										</div> -->
									<div class="d-flex container_content">
										<strong class="margin_content_inline">Địa chỉ:</strong>
										<p>{{ dataPC.petitioner_address }}</p>
									</div>
									<div class="d-flex container_content">
										<strong class="margin_content_inline"
											>Mục đích thẩm định:</strong
										><span id="appraise_purpose" class="text-left">{{
											dataPC.appraise_purpose
												? dataPC.appraise_purpose.name.length > 60
													? dataPC.appraise_purpose.name.substring(60, 0) +
													  "..."
													: dataPC.appraise_purpose.name
												: ""
										}}</span>
										<b-tooltip
											v-if="dataPC.appraise_purpose"
											target="appraise_purpose"
											placement="top-right"
											>{{ dataPC.appraise_purpose.name }}</b-tooltip
										>
									</div>

									<div class="d-flex container_content">
										<strong class="margin_content_inline">Loại sơ bộ:</strong>
										<p>
											{{ dataPC.pre_type ? dataPC.pre_type.description : "" }}
										</p>
									</div>
									<div class="d-flex container_content">
										<strong class="margin_content_inline"
											>Thời điểm sơ bộ:</strong
										>
										<p>
											{{ dataPC.pre_date ? formatDate(dataPC.pre_date) : "" }}
										</p>
									</div>
									<div class="d-flex container_content">
										<strong class="margin_content_inline"
											>Tổng phí dịch vụ:</strong
										>
										<p>
											{{
												dataPC.total_service_fee
													? formatNumber(dataPC.total_service_fee)
													: 0
											}}đ
										</p>
									</div>
									<div class="d-flex container_content">
										<strong class="margin_content_inline">Chiết khấu:</strong>
										<p>
											{{ dataPC.commission_fee ? dataPC.commission_fee : 0 }}%
										</p>
									</div>
									<div class="d-flex flex-column container_content">
										<strong class="margin_content_inline">Ghi chú:</strong>
										<!-- <span id="pre_asset_name" class="text-left"
										>{{ // dataPC.pre_asset_name && dataPC.pre_asset_name.length
										// > 25 ? dataPC.pre_asset_name.substring(25, 0) + "..." //
										// : dataPC.pre_asset_name dataPC.pre_asset_name ?
										dataPC.pre_asset_name.replace("\n", "<br />") : "" }}</span
									> -->
										<div
											id="pre_asset_name"
											class="text-left"
											v-html="formattedText"
										></div>
										<!-- <b-tooltip target="pre_asset_name" placement="top-right">{{
										dataPC.pre_asset_name
									}}</b-tooltip> -->
									</div>
									<div
										v-if="dataPC.cancel_reason_string"
										class="d-flex container_content"
									>
										<strong class="margin_content_inline"
											>Lý do hủy sơ bộ:</strong
										>
										<p>
											{{ dataPC.cancel_reason_string }}
										</p>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-lg-6 mt-1 d-grid h-100">
								<div class="row h-100">
									<div class="col-12">
										<div class="detail_certificate_2">
											<div class="d-flex container_content">
												<strong class="margin_content_inline"
													>Nhóm đối tác:</strong
												>
												<p>
													{{
														dataPC.customer_group
															? dataPC.customer_group.description
															: ""
													}}
												</p>
											</div>
											<div class="d-flex container_content">
												<strong class="margin_content_inline">Đối tác:</strong>
												<p>{{ dataPC.customer ? dataPC.customer.name : "" }}</p>
											</div>
											<div class="d-flex container_content">
												<strong class="margin_content_inline">Địa chỉ:</strong>
												<p>
													{{ dataPC.customer ? dataPC.customer.address : "" }}
												</p>
											</div>
											<div class="d-flex container_content">
												<strong class="margin_content_inline">Liên hệ:</strong>
												<p>
													{{ dataPC.customer ? dataPC.customer.phone : "" }}
												</p>
											</div>
										</div>
									</div>
									<div class="col-12 mt-1 mt-lg-4 ">
										<div class="detail_certificate_2">
											<div
												class="d-flex container_content justify-content-between"
											>
												<div class="d-flex">
													<strong class="margin_content_inline"
														>Nhân viên kinh doanh:</strong
													>
													<p>
														{{
															dataPC.appraiser_sale
																? dataPC.appraiser_sale.name
																: ""
														}}
													</p>
												</div>
												<div
													v-if="
														(editAppraiser && edit) ||
															isBusinessManager ||
															byPassAdmin
													"
													@click="handleShowAppraisal"
													class="btn-edit"
												>
													<img src="@/assets/icons/ic_edit_3.svg" alt="add" />
												</div>
											</div>
											<div class="d-flex container_content">
												<strong class="margin_content_inline"
													>Quản lý nghiệp vụ:</strong
												>
												<p>
													{{
														dataPC.appraiser_business_manager
															? dataPC.appraiser_business_manager.name
															: ""
													}}
												</p>
											</div>
											<div class="d-flex container_content">
												<strong class="margin_content_inline"
													>Chuyên viên thực hiện:</strong
												>
												<p>
													{{
														dataPC.appraiser_perform
															? dataPC.appraiser_perform.name
															: ""
													}}
												</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div v-else class="card-body card-info">
						<div class="row justify-content-between">
							<div class="col-md-12 col-lg-6 mt-1 d-grid h-100">
								<div class="detail_certificate_1 h-100">
									<div class="d-flex justify-content-between">
										<div class="d-flex container_content flex-column">
											<strong class="margin_content_inline">Khách hàng:</strong>
											<p>{{ dataPC.petitioner_name }}</p>
										</div>
										<div
											v-if="editInfo && edit"
											@click="handleShowAppraiseInformation"
											class="btn-edit d-flex align-items-start"
										>
											<img src="@/assets/icons/ic_edit_3.svg" alt="add" />
										</div>
									</div>
									<div class="row d-flex container_content flex-column">
										<strong class="margin_content_inline"
											>MST/CMND/CCCD/Passport:</strong
										>
										<p>{{ dataPC.petitioner_identity_card }}</p>
										<strong class="margin_content_inline">Điện thoại:</strong>
										<p>{{ dataPC.petitioner_phone }}</p>
									</div>
									<!-- <div class="d-flex container_content">
											<strong class="margin_content_inline">Điện thoại:</strong> <p>{{dataPC.petitioner_phone}}</p>
										</div> -->
									<div class="d-flex container_content flex-column">
										<strong class="margin_content_inline">Địa chỉ:</strong>
										<p>{{ dataPC.petitioner_address }}</p>
									</div>
									<div class="d-flex container_content flex-column">
										<strong class="margin_content_inline"
											>Mục đích thẩm định:</strong
										><span id="appraise_purpose" class="text-left">{{
											dataPC.appraise_purpose
												? dataPC.appraise_purpose.name
												: ""
										}}</span>
										<b-tooltip
											v-if="dataPC.appraise_purpose"
											target="appraise_purpose"
											placement="top-right"
											>{{ dataPC.appraise_purpose.name }}</b-tooltip
										>
									</div>

									<div class="d-flex container_content flex-column">
										<strong class="margin_content_inline">Loại sơ bộ:</strong>
										<p>
											{{ dataPC.pre_type ? dataPC.pre_type.description : "" }}
										</p>
									</div>
									<div class="d-flex container_content flex-column">
										<strong class="margin_content_inline"
											>Thời điểm sơ bộ:</strong
										>
										<p>
											{{ dataPC.pre_date ? formatDate(dataPC.pre_date) : "" }}
										</p>
									</div>
									<div class="d-flex container_content flex-column">
										<strong class="margin_content_inline"
											>Tổng phí dịch vụ:</strong
										>
										<p>
											{{
												dataPC.total_service_fee
													? formatNumber(dataPC.total_service_fee)
													: 0
											}}đ
										</p>
									</div>
									<div class="d-flex container_content flex-column">
										<strong class="margin_content_inline">Chiết khấu:</strong>
										<p>
											{{ dataPC.commission_fee ? dataPC.commission_fee : 0 }}%
										</p>
									</div>
									<div class="d-flex flex-column container_content">
										<strong class="margin_content_inline">Ghi chú:</strong>
										<!-- <span id="pre_asset_name" class="text-left"
										>{{ // dataPC.pre_asset_name && dataPC.pre_asset_name.length
										// > 25 ? dataPC.pre_asset_name.substring(25, 0) + "..." //
										// : dataPC.pre_asset_name dataPC.pre_asset_name ?
										dataPC.pre_asset_name.replace("\n", "<br />") : "" }}</span
									> -->
										<div
											id="pre_asset_name"
											class="text-left"
											v-html="formattedText"
										></div>
										<!-- <b-tooltip target="pre_asset_name" placement="top-right">{{
										dataPC.pre_asset_name
									}}</b-tooltip> -->
									</div>
									<div
										v-if="dataPC.cancel_reason_string"
										class="d-flex container_content"
									>
										<strong class="margin_content_inline"
											>Lý do hủy sơ bộ:</strong
										>
										<p>
											{{ dataPC.cancel_reason_string }}
										</p>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-lg-6 mt-1 d-grid h-100">
								<div class="row h-100">
									<div class="col-12">
										<div class="detail_certificate_2">
											<div class="d-flex container_content flex-column">
												<strong class="margin_content_inline"
													>Nhóm đối tác:</strong
												>
												<p>
													{{
														dataPC.customer_group
															? dataPC.customer_group.description
															: ""
													}}
												</p>
											</div>
											<div class="d-flex container_content flex-column">
												<strong class="margin_content_inline">Đối tác:</strong>
												<p>{{ dataPC.customer ? dataPC.customer.name : "" }}</p>
											</div>
											<div class="d-flex container_content flex-column">
												<strong class="margin_content_inline">Địa chỉ:</strong>
												<p>
													{{ dataPC.customer ? dataPC.customer.address : "" }}
												</p>
											</div>
											<div class="d-flex container_content flex-column">
												<strong class="margin_content_inline">Liên hệ:</strong>
												<p>
													{{ dataPC.customer ? dataPC.customer.phone : "" }}
												</p>
											</div>
										</div>
									</div>
									<div class="col-12 mt-1 mt-lg-4 ">
										<div class="detail_certificate_2">
											<div
												class="d-flex container_content justify-content-between "
											>
												<div class="d-flex flex-column">
													<strong class="margin_content_inline"
														>Nhân viên kinh doanh:</strong
													>
													<p>
														{{
															dataPC.appraiser_sale
																? dataPC.appraiser_sale.name
																: ""
														}}
													</p>
												</div>
												<div
													v-if="
														(editAppraiser && edit) ||
															isBusinessManager ||
															byPassAdmin
													"
													@click="handleShowAppraisal"
													class="btn-edit d-flex align-items-start"
												>
													<img src="@/assets/icons/ic_edit_3.svg" alt="add" />
												</div>
											</div>
											<div class="d-flex container_content flex-column">
												<strong class="margin_content_inline"
													>Quản lý nghiệp vụ:</strong
												>
												<p>
													{{
														dataPC.appraiser_business_manager
															? dataPC.appraiser_business_manager.name
															: ""
													}}
												</p>
											</div>
											<div class="d-flex container_content flex-column">
												<strong class="margin_content_inline"
													>Chuyên viên thực hiện:</strong
												>
												<p>
													{{
														dataPC.appraiser_perform
															? dataPC.appraiser_perform.name
															: ""
													}}
												</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<PaymentPreCertificateHistories
				v-if="!isMobile"
				@updatePayments="updatePayments"
			/>
			<div class="btn-history">
				<button class="btn btn-orange btn-history" @click="showDrawer">
					<img src="@/assets/icons/ic_log_history.svg" alt="history" />
				</button>
			</div>
			<a-drawer
				v-if="!isMobile"
				width="400"
				title="Lịch sử hoạt động"
				placement="right"
				:visible="visible"
				@close="onClose"
			>
				<a-timeline>
					<a-timeline-item
						v-for="(item, index) in historyList"
						:key="index"
						color="red"
					>
						<template #dot>
							<img
								class="dot-image"
								:src="
									item.causer && item.causer.image
										? item.causer.image
										: 'https://upload.wikimedia.org/wikipedia/commons/b/bc/Unknown_person.jpg'
								"
								style="width: 2em"
							/>
						</template>
						<p>
							<strong>{{
								item.causer && item.causer.name
									? item.causer.name
									: "Không xác	định"
							}}</strong>
						</p>
						<p>{{ item.description }}</p>
						<p
							:class="`${getHistoryTextColor[index]}`"
							v-if="item.properties.reason_id && item.reason_description"
						>
							Lí do : {{ item.reason_description }}
						</p>
						<p
							:class="`${getHistoryTextColor[index]}`"
							v-if="item.properties.note"
						>
							Ghi chú : {{ item.properties.note }}
						</p>
						<p>{{ formatDateTime(item.updated_at) }}</p>
					</a-timeline-item>
				</a-timeline>
			</a-drawer>
			<a-drawer
				v-else
				width="100%"
				title="Lịch sử hoạt động"
				placement="right"
				:visible="visible"
				@close="onClose"
			>
				<a-timeline style="padding-bottom: 10px">
					<a-timeline-item
						v-for="(item, index) in historyList"
						:key="index"
						color="red"
					>
						<template #dot>
							<img
								class="dot-image"
								:src="
									item.causer && item.causer.image
										? item.causer.image
										: 'https://upload.wikimedia.org/wikipedia/commons/b/bc/Unknown_person.jpg'
								"
								style="width: 2em"
							/>
						</template>
						<p>
							<strong>{{
								item.causer && item.causer.name
									? item.causer.name
									: "Không xác	định"
							}}</strong>
						</p>
						<p>{{ item.description }}</p>
						<p
							:class="`${getHistoryTextColor[index]}`"
							v-if="item.properties.reason_id && item.reason_description"
						>
							Lí do : {{ item.reason_description }}
						</p>
						<p
							:class="`${getHistoryTextColor[index]}`"
							v-if="item.properties.note"
						>
							Ghi chú : {{ item.properties.note }}
						</p>
						<p>{{ formatDateTime(item.updated_at) }}</p>
					</a-timeline-item>
				</a-timeline>
			</a-drawer>
			<div
				v-if="dataPC.id && dataPC.status >= 2 && dataPC.status != 8"
				class="col-12"
				:style="isMobile ? { padding: '0' } : {}"
			>
				<div class="card">
					<div class="card-title">
						<div class="d-flex justify-content-between align-items-center">
							<div
								class="row d-flex justify-content-between align-items-center"
							>
								<h3 class="title">Kết quả sơ bộ</h3>
							</div>
							<div
								v-if="
									dataPC.price_estimates.length > 0 &&
										allowEditFile.result &&
										edit
								"
								@click="showPriceEstimateListDialog = true"
								class="btn-edit"
							>
								<img src="@/assets/icons/ic_edit_3.svg" alt="add" />
							</div>
						</div>
					</div>
					<!-- <div class="card-body card-info">
					<div class="row">
						<div class="col-12 mt-2 table-wrapper">
							<a-table
								bordered
								:columns="dataColumn"
								:data-source="computedResultPreCertificate"
								table-layout="top"
								class="table_appraise_list"
								:rowKey="record => record.id"
							>
								<template slot="asset" slot-scope="asset">
									<p :id="asset.id" class="text-none mb-0">{{ asset.name }}</p>
								</template>
								<template
									slot="total_preliminary_value"
									slot-scope="total_preliminary_value"
								>
									<p class="text-none mb-0">
										{{
											total_preliminary_value
												? formatNumber(total_preliminary_value)
												: 0
										}}
										đ
									</p>
								</template>
							</a-table>
						</div>
					</div>
				</div>
				<OtherFile
					class="ml-2 mt-n3"
					v-if="showCardDetailFileResult && !dialogRequireForStage3"
					type="Result"
					:allow-edit="false"
				/> -->
					<div class="card-body card-info">
						<div class="row">
							<div
								v-if="
									dataPC.price_estimates.length === 0 &&
										dataPC.appraiser_perform &&
										dataPC.status === 2 &&
										(edit || add) &&
										user_id === dataPC.appraiser_perform.user_id
								"
								class="col-12 d-flex mt-2 justify-content-center"
							>
								<button
									class="btn btn_list_appraise-orange text-nowrap mr-3"
									@click.prevent="handleShowAppraiseList"
								>
									Chọn tài sản sơ bộ
								</button>
							</div>
							<div class="col-12" v-if="checkNoticeMessage()">
								<div class="infor-box">
									<svg
										style="margin-right: 1rem"
										width="12"
										height="13"
										viewBox="0 0 12 13"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
									>
										<path
											d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
											fill="#007EC6"
										/>
									</svg>
									Hồ sơ chưa khai báo thông tin tài sản sơ bộ
								</div>
							</div>
							<div
								v-if="dataPC.price_estimates.length > 0"
								class="col-12 mt-2 table-wrapper"
							>
								<a-table
									:columns="columnAssets"
									:data-source="dataPC.price_estimates"
									table-layout="top"
									class="table_appraise_list"
									:rowKey="record => record.id"
								>
									<template slot="asset" slot-scope="asset">
										<p :id="asset.id" class="text-none mb-0">
											{{ asset.full_address }}
										</p>
										<!-- <b-tooltip :target="(asset.id).toString()">{{asset.name}}</b-tooltip> -->
									</template>

									<template slot="version" slot-scope="version">
										<p class="text-none mb-0">{{ version }}</p>
									</template>
									<template slot="area" slot-scope="area">
										<p class="text-none mb-0">
											{{ area ? formatNumber(area) : 0 }} m <sup>2</sup>
										</p>
									</template>
									<template slot="price" slot-scope="price">
										<p class="text-none mb-0">
											{{ price ? formatNumber(price) : 0 }} đ
										</p>
									</template>
									<template slot="asset_type" slot-scope="asset_type">
										<p class="text-none mb-0">
											{{ asset_type ? formatCapitalize(asset_type) : "" }}
										</p>
									</template>
									<template slot="data" slot-scope="data">
										<div
											:style="isMobile ? { 'margin-top': '35px' } : {}"
											class="d-flex flex-column align-items-center"
										>
											<button
												v-if="!isMobile"
												class="link-detail text-none mb-0"
												@click.prevent="handleDetail(data)"
											>
												{{
													`${showAcronym(
														data.asset_type.dictionary_acronym
													)}_` + data.price_estimate_id
												}}
											</button>
											<button v-else class="link-detail-mobile text-none mb-0">
												{{
													`${showAcronym(
														data.asset_type.dictionary_acronym
													)}_` + data.price_estimate_id
												}}
											</button>

											<button
												v-if="isMobile"
												class="btn btn-orange btn-print btn-extra"
												@click="handlePrint(data.price_estimate_id)"
											>
												<!-- <font-awesome-icon icon="print" /> -->
												<img
													src="@/assets/icons/ic_printer_white.svg"
													alt="print"
												/>
											</button>
										</div>
									</template>
									<template slot="action" slot-scope="text, record">
										<button
											v-if="!isMobile"
											class="btn btn-orange btn-print btn-extra"
											@click="handlePrint(record.price_estimate_id)"
										>
											<!-- <font-awesome-icon icon="print" /> -->
											<img
												src="@/assets/icons/ic_printer_white.svg"
												alt="print"
											/>
										</button>
									</template>
								</a-table>
							</div>
							<div
								v-if="dataPC.price_estimates.length > 0"
								class="d-flex col-12 justify-content-end mt-3"
							>
								<span class="total mt-1">Tổng cộng</span>
								<div class="d-flex container_total justify-content-between">
									<div>
										{{
											totalPricePriceEstimate
												? formatNumber(totalPricePriceEstimate)
												: 0
										}}
									</div>
									<div class="ml-1">VNĐ</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- <div
			v-if="dataPC.id && showExportDocument && dataPC.status !== 1"
			class="col-12"
			:style="isMobile ? { padding: '0' } : {}"
		>
			<DocumentExport
				:allow-edit="editExportDocument"
				:is_pc="true"
				:data-id="dataPC.id"
				:lstFileExport="dataPC.export_documents || []"
				:permission="{ allowExport: exportAction }"
				:toast="$toast"
			/>
		</div> -->
			<div
				v-if="dataPC.id"
				class="col-12"
				:style="isMobile ? { padding: '0' } : {}"
			>
				<OtherFile
					:type="'Appendix'"
					:allow-edit="allowEditFile.appendix"
					:from-component="'Detail'"
				/>
			</div>

			<Footer
				v-if="jsonConfig && profile && dataPC && dataPC.id"
				:style="isMobile ? {} : {}"
				:key="dataPC.status"
				:form="dataPC"
				:jsonConfig="jsonConfig"
				:status="dataPC.status"
				:profile="profile"
				:idData="dataPC.id"
				:checkVersion="checkVersion"
				:certificateId="dataPC.certificate_id"
				@handleFooterAccept="handleFooterAccept"
				@handleFooterRedistributeRecord="handleFooterRedistributeRecord"
				@handleEdit="handleEdit"
				@onCancel="onCancel"
				@viewAppraiseListVersion="viewAppraiseListVersion"
			/>
			<ModalPCAppraisal
				:key="key_render_appraisal"
				v-if="showAppraisalDialog"
				@cancel="showAppraisalDialog = false"
				@updateAppraisal="updateAppraisal"
			/>

			<ModalPCAppraiseInfomation
				v-if="showAppraiseInformationDialog"
				@cancel="showAppraiseInformationDialog = false"
				@updateAppraiseInformation="updateAppraiseInformation"
			/>
			<ModalViewDocument
				v-if="isShowPrint"
				@cancel="isShowPrint = false"
				:filePrint="filePrint"
				:title="title"
			/>
			<ModalDelete
				v-if="openModalDelete"
				@cancel="openModalDelete = false"
				@action="handleDelete"
			/>
			<ModalNotificationWithAssign
				v-if="isHandleAction"
				@cancel="isHandleAction = false"
				:notification="
					message == 'Từ chối' ||
					message == 'Khôi phục' ||
					message == 'Hủy' ||
					message == 'Phân lại'
						? `Bạn có muốn '${message}' hồ sơ này ${
								message == 'Phân lại'
									? ` ở bước '` + statusDescription + `' `
									: ''
						  }? `
						: `Bạn có muốn chuyển hồ sơ này sang trạng thái`
				"
				workflowName="ycsbConfig"
				:status_text="message"
				:status_next="targetStatus"
				:dataHSTD="dataPC"
				:appraiser="appraiserChangeStage"
				@action="handleAction2"
			/>

			<ModalDelete
				v-if="deleteUploadDocument"
				@cancel="deleteUploadDocument = false"
				@action="deleteDocument"
			/>
			<ModalRequireForStage3
				v-if="dialogRequireForStage3"
				:notification="
					`Bạn có muốn chuyển yêu cầu này sang trạng thái 'Định giá sơ bộ'?`
				"
				@cancel="dialogRequireForStage3 = false"
			/>

			<ModalPriceEstimateList
				v-if="showPriceEstimateListDialog"
				:data="dataPC"
				:idData="dataPC.id"
				@updatePriceEstimateList="updatePriceEstimateList"
				@cancel="showPriceEstimateListDialog = false"
			/>

			<ModalPrintEstimateAssets
				v-if="openPrint"
				@cancel="openPrint = false"
				:data="priceEstimates"
				:isApartment="miscVariable.isApartment"
			/>
		</div>
		<template #overlay>
			<div class="text-center">
				<b-icon font-scale="3" animation="cylon"></b-icon>
				<p id="cancel-label">Đang in...</p>
				<b-button
					ref="cancel"
					variant="outline-danger"
					size="sm"
					aria-describedby="cancel-label"
					@click="showLoadingPrint = false"
				>
					Thoát
				</b-button>
			</div>
		</template>
	</b-overlay>
</template>
<style lang="scss">
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "../../../node_modules/leaflet/dist/leaflet.css";
</style>
<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePreCertificateStore } from "@/store/preCertificate";
import { usePriceEstimatesStore } from "@/store/priceEstimates";

import ModalPrintEstimateAssets from "@/components/Modal/ModalPrintEstimateAssetNew";
import ModalDelete from "@/components/Modal/ModalDelete";
import ModalViewDocument from "@/components/PreCertificate/ModalViewDocument";
import ModalPriceEstimateList from "@/components/PreCertificate/ModalPriceEstimateList";
import ModalNotificationCertificate from "@/components/Modal/ModalNotificationCertificate";
import ModalNotificationPreCertificateNote from "@/components/PreCertificate/ModalNotificationPreCertificateNote";
import ModalNotificationWithAssign from "@/components/Modal/ModalNotificationWithAssign";

import InputDatePicker from "@/components/Form/InputDatePicker";
import InputCategory from "@/components/Form/InputCategory";
import InputCategorySearch from "@/components/Form/InputCategorySearch";
import InputText from "@/components/Form/InputText";
import InputDatePickerV2 from "@/components/Form/InputDatePickerV2";
import InputTextPrefixCustom from "@/components/Form/InputTextPrefixCustom";
import InputNumberFormat from "@/components/Form/InputNumber";
import InputCurrency from "@/components/Form/InputCurrency";
import { Tabs, TabItem } from "vue-material-tabs";
import Vue from "vue";
import Icon from "buefy";
import InputLengthArea from "@/components/Form/InputLengthArea.vue";
import PreCertificate from "@/models/PreCertificate";
import Certificate from "@/models/Certificate";
import WareHouse from "@/models/WareHouse";
import { Timeline, Drawer } from "ant-design-vue";
import moment from "moment";
import ModalCustomer from "@/components/PreCertificate/ModalCustomer";
import ModalPCAppraisal from "@/components/PreCertificate/ModalPCAppraisal";
import PaymentPreCertificateHistories from "@/components/PreCertificate/PaymentPreCertificateHistories";
import ModalPCAppraiseInfomation from "@/components/PreCertificate/ModalPCAppraiseInfomation";
import ModalRequireForStage3 from "@/components/PreCertificate/ModalRequireForStage3";
import OtherFile from "@/components/PreCertificate/OtherFile";
import DocumentExport from "@/components/PreCertificate/DocumentExport";
import File from "@/models/File";
import axios from "@/plugins/axios";
import {
	BIconStopwatch,
	BIcon,
	BButton,
	BTooltip,
	BOverlay,
	BDropdown,
	BDropdownItem,
	BButtonGroup
} from "bootstrap-vue";
import Footer from "@/components/PreCertificate/FooterDetail.vue";
import IconBase from "./../IconBase.vue";
import pdfFonts from "@/assets/resources/vfs_fonts_open";

Vue.use(Icon);
export default {
	props: {
		routeId: {
			type: String
		}
	},
	name: "detail_pre_certification",
	components: {
		ModalPrintEstimateAssets,
		ModalPriceEstimateList,
		DocumentExport,
		ModalNotificationWithAssign,
		PaymentPreCertificateHistories,
		IconBase,
		OtherFile,
		InputCategory,
		InputCategorySearch,
		InputText,
		InputDatePickerV2,
		InputTextPrefixCustom,
		InputNumberFormat,
		InputDatePicker,
		InputCurrency,
		Tabs,
		TabItem,
		Timeline,
		Drawer,
		InputLengthArea,
		ModalCustomer,
		ModalPCAppraisal,
		ModalPCAppraiseInfomation,
		"b-tooltip": BTooltip,
		ModalNotificationCertificate,
		ModalViewDocument,
		ModalDelete,
		"b-dropdown-item": BDropdownItem,
		"b-button-group": BButtonGroup,
		"b-dropdown": BDropdown,
		"b-overlay": BOverlay,
		"b-icon": BIconStopwatch,
		"b-button": BButton,
		Footer,
		ModalNotificationPreCertificateNote,
		ModalRequireForStage3
	},
	data() {
		return {
			dataColumn: [
				{
					title: "Tên tài sản sơ bộ",
					align: "left",
					scopedSlots: { customRender: "asset" },
					hiddenItem: false
				},

				{
					title: "Tổng giá trị",
					align: "right",
					scopedSlots: { customRender: "total_preliminary_value" },
					dataIndex: "total_preliminary_value",
					hiddenItem: false
				}
			],
			openPrint: false,
			theme: {
				navItem: "#000000",
				navActiveItem: "#FAA831",
				slider: "#FAA831",
				arrow: "#000000"
			},
			status: 1,
			total_price_price_estimates: "",
			key_render_appraisal: 10000,
			openModalDelete: false,
			showCustomerDialog: false,
			showLoadingPrint: false,
			showAppraiseInformationDialog: false,
			showAppraisalDialog: false,
			showPriceEstimateListDialog: false,
			visible: false,
			showCardDetailImage: true,
			openSendRequire: false,
			openSendAppraiser: false,
			customers_step_1: this.customers,
			appraisersManager: [],
			appraisers: [],
			signAppraisers: [],
			file: "",
			documentAppraise: [],
			message: "",
			cancel_certificate: false,
			openNotificationDenined: false,
			filePrint: "",
			isShowPrint: false,
			title: "",
			indexDelete: "",
			id_file_delete: "",
			isBusinessManager: false,
			byPassAdmin: false,
			showDetailAppraise: false,
			dataDetailAppraise: [],
			appraiser_number: "",
			historyList: [],
			isCheckRealEstate: true,
			isCheckConstruction: false,
			isViewAutomationDocument: true,
			targetStatus: "",
			isHandleAction: false,
			checkVersion: true,
			typeAppraiseProperty: [],
			isShowAppraiseListVersion: false,
			isCheckPrice: false,
			isCheckVersion: false,
			isCheckLegal: false,
			changeStatusRequire: {},
			isApartment: false,
			reportType: "",
			deleteUploadDocument: false,
			documentName: [
				"Chứng thư thẩm định",
				"Báo cáo thẩm định",
				"Bảng điều chỉnh QSDĐ",
				"Bảng điều chỉnh CTXD",
				"Hình ảnh hiện trạng",
				"Phiếu thu thập TSSS"
			]
		};
	},
	setup(props) {
		const checkMobile = () => {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		};
		const isMobile = ref(checkMobile());
		const preCertificateStore = usePreCertificateStore();
		const priceEstimateStore = usePriceEstimatesStore();
		const { priceEstimates, miscVariable } = storeToRefs(priceEstimateStore);

		const {
			dataPC,
			lstDataConfig,
			preCertificateOtherDocuments,
			jsonConfig,
			vueStoree,
			other,
			totalPricePriceEstimate
		} = storeToRefs(preCertificateStore);
		const dialogRequireForStage3 = ref(false);
		const config = ref({});
		const editInfo = ref(false);
		const editAppraiser = ref(false);
		const editPayments = ref(false);
		const allowEditFile = ref({ appendix: false, result: false });
		const showExportDocument = ref(false);
		const editExportDocument = ref(false);
		const changeEditStatus = async () => {
			let dataJson = jsonConfig.value.principle.filter(
				item => item.status === dataPC.value.status && item.isActive === 1
			);
			if (dataJson && dataJson.length > 0) {
				config.value = dataJson[0];

				const checkPermissionObject = {
					form: false,
					info: false,
					file_appendix: false,
					file_result: false,
					payments: false,
					appraiser: false
				};
				for (const key of Object.keys(checkPermissionObject)) {
					checkPermissionObject[key] = await checkPermissionRequire(
						key,
						config.value
					);
				}
				showExportDocument.value = dataJson[0].isExportDocument;
				editExportDocument.value = dataJson[0].edit.export_document
					? dataJson[0].edit.export_document
					: false;
				editAppraiser.value =
					checkPermissionObject.appraiser && dataJson[0].edit.appraiser
						? dataJson[0].edit.appraiser
						: false;

				editInfo.value =
					checkPermissionObject.info && dataJson[0].edit.info
						? dataJson[0].edit.info
						: false;
				editPayments.value =
					checkPermissionObject.payments && dataJson[0].edit.payments
						? dataJson[0].edit.payments
						: false;
				allowEditFile.value.appendix =
					checkPermissionObject.file_appendix && dataJson[0].edit.file_appendix
						? dataJson[0].edit.file_appendix
						: false;
				allowEditFile.value.result =
					checkPermissionObject.file_result && dataJson[0].edit.file_result
						? dataJson[0].edit.file_result
						: false;
				const tempPermission = {
					edit: edit.value,
					editPayments: dataJson[0].edit.payments || false
				};
				preCertificateStore.updatePermission(tempPermission);
			}
		};

		const checkPermissionRequire = (key, config) => {
			const permissionAllowEdit = jsonConfig.value.permissionAllowEdit;
			const user = vueStoree.value.user;
			if (
				config.put_require_roles &&
				user.roles &&
				config.put_require_roles.includes(user.roles[0].name)
			) {
				return true;
			}
			if (permissionAllowEdit[key]) {
				for (let index = 0; index < permissionAllowEdit[key].length; index++) {
					const element = permissionAllowEdit[key][index];
					if (
						// (element === "created_by" && dataPC.value.created_by === user.id) ||
						element !== "created_by" &&
						dataPC.value[element] === user.appraiser.id
					) {
						return true;
					}
				}
			}
			return false;
		};
		const profile = ref(null);
		const view = ref(false);
		const add = ref(false);
		const edit = ref(false);
		const deleted = ref(false);
		const accept = ref(false);
		const checkRole = ref(false);
		const exportAction = ref(false);
		const user_id = ref("");
		const isBusinessManager = ref(false);
		const byPassAdmin = ref(false);
		const permissionFunction = async () => {
			profile.value = vueStoree.value.profile;
			user_id.value = vueStoree.value.user.id;
			if (user_id.value === dataPC.value.created_by) {
				checkRole.value = true;
			}
			if (
				dataPC.value &&
				dataPC.value.appraiser_business_manager &&
				dataPC.value.appraiser_business_manager.user_id ===
					vueStoree.value.user.id
			) {
				isBusinessManager.value = true;
			}
			if (
				dataPC.value &&
				dataPC.value.status !== 6 &&
				dataPC.value.status !== 7 &&
				(vueStoree.value.user.roles[0].name === "ADMIN" ||
					vueStoree.value.user.roles[0].name === "ROOT_ADMIN")
			) {
				byPassAdmin.value = true;
			}
			const permission = vueStoree.value.currentPermissions;
			permission.forEach(value => {
				if (value === "VIEW_PRE_CERTIFICATE") {
					view.value = true;
				}
				if (value === "ADD_PRE_CERTIFICATE") {
					add.value = true;
				}
				if (value === "EDIT_PRE_CERTIFICATE") {
					edit.value = true;
				}
				if (value === "DELETE_PRE_CERTIFICATE") {
					deleted.value = true;
				}
				if (value === "ACCEPT_PRE_CERTIFICATE") {
					accept.value = true;
				}
				if (value === "EXPORT_PRE_CERTIFICATE") {
					exportAction.value = true;
				}
			});
			if (!view.value) {
				other.value.router.push({ name: "page-not-found" });
				other.value.toast.open({
					message: "Bạn ko có quyền xem yêu cầu sơ bộ",
					type: "error",
					position: "top-right",
					duration: 5000
				});
			}
		};
		const start = async () => {
			if (!jsonConfig.value) {
				jsonConfig.value = await preCertificateStore.getConfig();
			}
			await preCertificateStore.resetData();

			dataPC.value = await preCertificateStore.getPreCertificate(props.routeId);
			await permissionFunction();
			await changeEditStatus();
		};

		start();
		const checkVersion2 = ref([]);
		const showCardDetailFileResult = ref(true);
		const showCardPCPayments = ref(false);
		const appraiserChangeStage = ref(null);

		return {
			priceEstimateStore,
			priceEstimates,
			miscVariable,

			totalPricePriceEstimate,
			editExportDocument,
			showExportDocument,
			appraiserChangeStage,
			showCardPCPayments,
			allowEditFile,
			jsonConfig,
			config,
			editAppraiser,
			editInfo,
			editPayments,
			dialogRequireForStage3,
			isMobile,
			dataPC,
			lstDataConfig,
			preCertificateOtherDocuments,
			preCertificateStore,
			checkVersion2,
			profile,
			view,
			add,
			edit,
			deleted,
			accept,
			checkRole,
			exportAction,
			user_id,

			showCardDetailFileResult,
			changeEditStatus
		};
	},

	// created() {
	// 	if (
	// 		this.dataPC &&
	// 		this.dataPC.appraiser_business_manager &&
	// 		this.dataPC.appraiser_business_manager.user_id === this.user_id
	// 	) {
	// 		this.isBusinessManager = true;
	// 	}
	// 	if (this.dataPC && this.dataPC.status !== 4 && this.dataPC.status !== 5) {
	// 		console.log(this.profile);
	// 		this.byPassAdmin = true;
	// 	}
	// },
	computed: {
		formattedText() {
			return this.dataPC.pre_asset_name
				? this.dataPC.pre_asset_name.replace(/\n/g, "<br>")
				: "";
		},
		statusDescription() {
			if (this.jsonConfig) {
				const status = this.jsonConfig.principle.find(
					i => i.status === this.dataPC.status
				);
				return status ? status.description : "";
			}

			return "";
		},
		getHistoryTextColor() {
			return this.historyList.map(item => {
				return this.loadColor(item);
			});
		},
		computedResultPreCertificate() {
			return [
				{
					name: this.dataPC.pre_asset_name,
					total_preliminary_value: this.dataPC.total_preliminary_value
				}
			];
		},
		columnAssets() {
			let dataColumn = [
				{
					title: "Mã TSSB",
					align: "left",
					scopedSlots: { customRender: "data" },
					hiddenItem: false
				},
				{
					title: "Version",
					align: "center",
					scopedSlots: { customRender: "version" },
					dataIndex: "last_version.version",
					hiddenItem: false
				},
				{
					title: "Loại tài sản",
					align: "left",
					scopedSlots: { customRender: "asset_type" },
					dataIndex: "asset_type.description",
					hiddenItem: false
				},
				{
					title: "Địa chỉ tài sản",
					align: "left",
					scopedSlots: { customRender: "asset" },
					hiddenItem: false
				},
				// {
				// 	title: 'Loại đất',
				// 	align: 'left',
				// 	scopedSlots: {customRender: 'land'},
				// 	hiddenItem: this.isCheckRealEstate
				// },
				{
					title: "Tổng diện tích",
					align: "right",
					scopedSlots: { customRender: "area" },
					dataIndex: "total_area",
					hiddenItem: !this.isCheckRealEstate
				},
				{
					title: "Tổng giá trị",
					align: "right",
					scopedSlots: { customRender: "price" },
					dataIndex: "total_price",
					hiddenItem: false
				},
				{
					title: "",
					dataIndex: "action",
					key: "action",
					scopedSlots: { customRender: "action" },
					hiddenItem: false
				}
				// {
				// 	title: 'Ngày tạo',
				// 	align: 'right',
				// 	scopedSlots: {customRender: 'created_at'},
				// 	dataIndex: 'created_at',
				// 	hiddenItem: false
				// }
			];
			return dataColumn.filter(item => item.hiddenItem === false);
		}
	},
	methods: {
		handleshowCardPCPayments() {
			if (this.dataPC.total_service_fee > 0) this.showCardPCPayments = true;
			else {
				this.$toast.open({
					message: "Vui lòng bổ sung tổng phí dịch vụ",
					type: "error",
					position: "top-right",
					duration: 5000
				});
			}
		},
		getReport(type) {
			let report = this.dataPC.other_documents.find(
				i => i.description === type
			);
			return report;
		},

		loadColor(item) {
			let color = "";
			if (item.log_name == "update_status") {
				if (item.description.includes("từ chối")) color = "text-danger";
				else if (item.description.includes("Hủy")) color = "text-danger";
				else color = "text-success";
			}
			return color;
		},
		formatSentenceCase(phrase) {
			let text = phrase.toLowerCase();
			return text.charAt(0).toUpperCase() + text.slice(1);
		},

		checkNoticeMessage() {
			if (
				this.dataPC.price_estimates.length === 0 &&
				this.dataPC.appraiser_perform &&
				this.user_id !== this.dataPC.appraiser_perform.user_id
			) {
				return true;
			} else if (
				this.dataPC.price_estimates.length === 0 &&
				this.dataPC.status === 6 &&
				this.dataPC.appraiser_perform &&
				this.user_id === this.dataPC.appraiser_perform.user_id
			) {
				return true;
			} else return false;
		},
		showAcronym(acronym) {
			// if (acronym === "KHAC") {
			// 	return "TSK";
			// } else if (acronym === "DS") {
			// 	return "DS";
			// } else return acronym;
			return "TSSB";
		},
		showDrawer() {
			this.visible = true;
			this.getHistoryTimeLine();
		},
		onClose() {
			this.visible = false;
		},
		async handleDetail(data) {
			let routeData;

			routeData = this.$router.resolve({
				name: "price_estimates.detail",
				query: {
					id: data.price_estimate_id
				}
			});
			window.open(routeData.href, "_blank");
		},
		handleDetailCertificate(id) {
			let url = this.$router.resolve({
				name: "certification_brief.detail",
				query: {
					id: id.toString()
				}
			}).href;

			window.open(url, "_blank");
		},
		async getHistoryTimeLine() {
			const res = await PreCertificate.getHistoryTimeline(this.dataPC.id);
			if (res.data) {
				const resp = await WareHouse.getDictionaries();
				if (resp) {
					this.historyList = res.data;
					for (let i = 0; i < this.historyList.length; i++) {
						let e = this.historyList[i];
						if (e.properties.reason_id) {
							let result = null;
							if (e.description.includes("Hủy")) {
								result = resp.data.li_do_huy_so_bo.filter(
									item => item.id === e.properties.reason_id
								);
							} else {
								result = resp.data.li_do.filter(
									item => item.id === e.properties.reason_id
								);
							}

							e.reason_description = result[0].description;
						}
					}
				}
			} else if (res.error) {
				return this.$toast.open({
					message: res.error.message,
					type: "error",
					position: "top-right",
					duration: 5000
				});
			}
		},
		formatDate(value) {
			return moment(String(value)).format("DD/MM/YYYY");
		},
		formatDatePDF(value) {
			return moment(String(value)).format("HH:mm [Ngày] DD/MM/YYYY");
		},
		formatDateTime(value) {
			return moment(String(value)).format("HH:mm DD/MM/YYYY");
		},
		formatNumber(num) {
			if (num) {
				let formatedNum = num.toString().replace(".", ",");
				return formatedNum.toString().replace(/^[+-]?\d+/, function(int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
			}
		},
		handleEdit() {
			this.$router
				.push({
					name: "pre_certification.edit",
					query: {
						id: `${this.dataPC.id}`
					}
				})
				.catch(_ => {});
		},
		onCancel() {
			return this.$router.push({ name: "pre_certification.index" });
		},

		handleShowAppraisal() {
			this.key_render_appraisal += 1;
			this.status = this.dataPC.status;
			this.showAppraisalDialog = true;
		},
		handleShowAppraiseInformation() {
			this.showAppraiseInformationDialog = true;
		},
		handleShowAppraiseList() {
			this.showPriceEstimateListDialog = true;
		},
		handleImportAppraise() {
			return this.$toast.open({
				message: "Hiện tại chức năng này chưa được mở ở phiên bản dùng thử",
				type: "error",
				position: "top-right",
				duration: 3000
			});
		},
		async updatePayments() {
			await this.preCertificateStore.getPreCertificate(this.routeId);
		},
		async updateAppraiseInformation() {
			await this.preCertificateStore.getPreCertificate(this.routeId);
		},
		async updatePriceEstimateList() {
			await this.preCertificateStore.getPreCertificate(this.routeId);
		},
		async updateAppraisal() {
			await this.preCertificateStore.getPreCertificate(this.routeId);
			await this.changeEditStatus();
			this.key_render_appraisal += 1;
			this.showAppraisalDialog = false;
		},

		checkAppraiser() {
			if (this.dataPC.appraiser_perform_id && this.dataPC.appraiser_id) {
				return true;
			} else {
				return false;
			}
		},
		checkItemList() {
			if (
				this.dataPC.personal_properties.length > 0 ||
				this.dataPC.general_asset.length > 0
			) {
				return true;
			} else {
				return false;
			}
		},
		checkRequired(require, data) {
			let message = "";
			let check = false;
			if (require) {
				this.changeStatusRequire = require;
				this.isCheckPrice = require.check_price ? require.check_price : false;
				this.isCheckLegal = require.check_legal ? require.check_legal : false;
				this.isCheckVersion = require.check_version
					? require.check_version
					: false;
			}
			return message;
		},
		handleFooterAccept(target) {
			console.log(target);
			this.appraiserChangeStage = null;

			// if (
			// 	target.description &&
			// 	target.description.toUpperCase() === "HOÀN THÀNH" &&
			// 	this.dataPC.debtRemain
			// ) {
			// 	if (
			// 		this.dataPC.payments &&
			// 		(this.dataPC.payments.length === 0 ||
			// 			(this.dataPC.payments.length === 1 && !this.dataPC.payments[0].id))
			// 	) {
			// 		this.$toast.open({
			// 			message:
			// 				"Vui lòng thanh toán hết dư nợ để chuyển sang trạng thái hoàn thành !",
			// 			type: "error",
			// 			position: "top-right",
			// 			duration: 3000
			// 		});

			// 		return;
			// 	} else if (
			// 		this.dataPC.payments &&
			// 		this.dataPC.payments.length > 0 &&
			// 		this.dataPC.payments[0].id
			// 	) {
			// 		let debt_remain = this.dataPC.service_fee
			// 			? Number(this.dataPC.service_fee)
			// 			: Number(this.dataPC.total_service_fee);
			// 		let amount_paid = 0;
			// 		for (let index = 0; index < this.dataPC.payments.length; index++) {
			// 			const element = this.dataPC.payments[index];
			// 			if (element.amount && element.amount > 0) {
			// 				amount_paid += parseFloat(element.amount);
			// 			}
			// 		}
			// 		if (debt_remain - amount_paid > 0) {
			// 			this.$toast.open({
			// 				message:
			// 					"Vui lòng thanh toán hết dư nợ  để chuyển sang trạng thái hoàn thành !",
			// 				type: "error",
			// 				position: "top-right",
			// 				duration: 3000
			// 			});
			// 			return;
			// 		}
			// 	}
			// }
			if (target.code && target.code === "chuyen_chinh_thuc") {
				// if (
				// 	!this.preCertificateOtherDocuments.Result ||
				// 	this.preCertificateOtherDocuments.Result.length === 0 ||
				// 	this.dataPC.total_preliminary_value == 0 ||
				// 	this.dataPC.total_preliminary_value == null ||
				// 	this.dataPC.total_preliminary_value == undefined
				// ) {
				// 	this.openMessage(
				// 		"Vui lòng bổ sung file kết quả sơ bộ và Tổng giá trị sơ bộ",
				// 		"error"
				// 	);
				// 	return;
				// }
				this.dataPC.target_code = target.code;
				this.message = target.description;
				this.isHandleAction = true;
				return;
			}
			let config = this.jsonConfig.principle.find(i => i.id === target.id);
			let message = "";
			if (config) {
				this.config = config;
				let require = config.require;
				message = this.checkDiffVersion();
				if (message === "" && require) {
					message = this.checkRequired(require, this.data);
				}
				if (message === "") {
					this.targetStatus = config.status;
					this.dataPC.target_status = config.status;
					this.dataPC.target_code = target.code;
					this.message = target.description;

					if (
						this.dataPC.status < this.targetStatus &&
						this.targetStatus == 3
					) {
						if (this.dataPC.price_estimates.length == 0) {
							this.openMessage("Vui lòng bổ sung tài sản sơ bộ");
							return;
						}
					}
					if (config.re_assign)
						this.appraiserChangeStage = {
							id: this.dataPC[config.re_assign],
							type: config.re_assign
						};
					this.isHandleAction = true;
				} else {
					this.openMessage(message);
				}
			} else {
				this.openMessage(
					"Không tìm thấy thông tin bước tiếp theo. Vui lòng liên hệ admin để hỗ trợ."
				);
			}
		},
		handleFooterRedistributeRecord(idStep2) {
			this.appraiserChangeStage = null;
			let config = this.jsonConfig.principle.find(i => i.id === idStep2);
			let message = "";
			if (config) {
				this.config = config;
				let require = config.require;
				message = this.checkDiffVersion();
				if (message === "" && require) {
					message = this.checkRequired(require, this.data);
				}
				if (message === "") {
					this.targetStatus = config.status;
					this.dataPC.target_status = config.status;
					this.dataPC.target_code = "";
					this.message = "Phân lại";

					if (config.re_assign)
						this.appraiserChangeStage = {
							id: this.dataPC[config.re_assign],
							type: config.re_assign
						};
					this.isHandleAction = true;
				} else {
					this.openMessage(message);
				}
			} else {
				this.openMessage(
					"Không tìm thấy thông tin bước tiếp theo. Vui lòng liên hệ admin để hỗ trợ."
				);
			}
		},
		getExpireStatusDate() {
			let dateConvert = new Date();
			let minutes = this.config.process_time ? this.config.process_time : 1440;
			let dateConverted = new Date(dateConvert.getTime() + minutes * 60000);
			let status_expired_at = moment(dateConverted).format("DD-MM-YYYY HH:mm");
			return status_expired_at;
		},
		async updateToOffical(note) {
			const res = await PreCertificate.updateToOfficalPreCertificate(
				this.dataPC.id,
				{ note }
			);
			if (res.data && res.data.error === false) {
				await this.preCertificateStore.getPreCertificate(this.routeId);
				this.changeEditStatus();
				await this.$toast.open({
					message: this.message + " thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				// this.key_dragg++;
			} else {
				await this.$toast.open({
					message: `${res.data.message}`,
					type: "error",
					position: "top-right",
					duration: 3000
				});
				this.handleCancelAccept2();
			}
			this.isMoved = false;
			this.showDetailPopUp = false;
			this.isHandleAction = false;
		},
		async handleAction2(note, reason_id, tempAppraiser, estime) {
			if (this.dataPC.target_code == "chuyen_chinh_thuc") {
				this.updateToOffical(note);
				return;
			}
			const res = await this.preCertificateStore.updateStatus(
				this.dataPC.id,
				note,
				reason_id,
				tempAppraiser,
				estime
			);

			if (res.data) {
				// this.dataPC.status = this.targetStatus;
				await this.preCertificateStore.getPreCertificate(this.routeId);

				this.changeEditStatus();
				this.$toast.open({
					message:
						this.message == "Từ chối" ||
						this.message == "Khôi phục" ||
						this.message == "Hủy" ||
						this.message == "Phân lại"
							? this.message + " thành công"
							: "Chuyển trạng thái " + `"${this.message}"` + " thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: "error",
					position: "top-right",
					duration: 5000
				});
			}
			this.isHandleAction = false;
		},

		async onImageChange(e) {
			const formData = new FormData();
			let check = true;
			let files = e.target.files;
			if (!files.length) {
				return;
			}
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i];
				if (
					this.file.type === "image/png" ||
					this.file.type === "image/jpeg" ||
					this.file.type === "image/jpg" ||
					this.file.type === "image/gif" ||
					this.file.type ===
						"application/vnd.openxmlformats-officedocument.wordprocessingml.document" ||
					this.file.type ===
						"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ||
					this.file.type === "application/pdf"
				) {
				} else {
					check = false;
					this.$toast.open({
						message: "Hình không đúng định dạng vui lòng kiểm tra lại",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			}
			if (check) {
				if (files.length) {
					for (let i = 0; i < files.length; i++) {
						formData.append("files[" + i + "]", files[i]);
					}
					let res = null;
					if (this.dataPC.status === 1) {
						res = await File.saleUploadFileCertificate(
							formData,
							this.dataPC.id
						);
					} else {
						res = await File.uploadFileCertificate(formData, this.dataPC.id);
					}
					if (res.data) {
						// await this.$emit('handleChangeFile', res.data.data)
						this.dataPC.other_documents = res.data.data;
						this.$toast.open({
							message: "Thêm file thành công",
							type: "success",
							position: "top-right",
							duration: 3000
						});
					}
				}
			}
		},

		async onUploadDocument(type, e) {
			const formData = new FormData();
			let check = true;
			let files = e.target.files;
			if (!files.length) {
				return;
			}
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i];
				if (
					this.file.type ===
						"application/vnd.openxmlformats-officedocument.wordprocessingml.document" ||
					this.file.type === "application/pdf"
				) {
				} else {
					check = false;
					this.$toast.open({
						message: "File dữ liệu không đúng định dạng vui lòng kiểm tra lại",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			}
			if (check) {
				if (files.length) {
					for (let i = 0; i < files.length; i++) {
						formData.append("files[" + i + "]", files[i]);
					}
					let res = null;
					res = await File.uploadDocument(formData, this.dataPC.id, type);
					if (res.data) {
						this.dataPC.other_documents = res.data.data;
						this.$toast.open({
							message: "Thêm file thành công",
							type: "success",
							position: "top-right",
							duration: 3000
						});
					}
				}
			}
		},
		async viewCertificate() {
			await Certificate.getPrintProof(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePrint = file.url;
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
			this.title = "Tài liệu chứng thư thẩm định";
			this.isShowPrint = true;
		},
		async downloadCertificate() {
			await Certificate.getPrintProof(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		async viewReportCertificate() {
			await Certificate.getPrintReport(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePrint = file.url;
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
			this.title = "Tài liệu báo cáo thẩm định";
			this.isShowPrint = true;
		},
		async downloadReportCertificate() {
			await Certificate.getPrintReport(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		async viewAssetDocument() {
			let arrayAsset = [];
			if (this.dataPC.real_estate && this.dataPC.real_estate.length > 0) {
				await this.dataPC.real_estate.forEach(item => {
					if (
						item.appraises &&
						item.appraises.appraise_has_assets &&
						item.appraises.appraise_has_assets.length > 0
					) {
						item.appraises.appraise_has_assets.forEach(asset => {
							arrayAsset.push(asset.asset_general_id);
						});
					}
					if (
						item.apartment &&
						item.apartment.apartment_has_assets &&
						item.apartment.apartment_has_assets.length > 0
					) {
						item.apartment.apartment_has_assets.forEach(asset => {
							arrayAsset.push(asset.asset_general_id);
						});
					}
				});
			}
			const res = await WareHouse.getPrint(arrayAsset, this.dataPC.id);
			if (res.data) {
				const file = res.data;
				this.filePrint = file.url;
				this.title = "Tài liệu phiếu thu thập TSSS";
				this.isShowPrint = true;
			} else {
				this.$toast.open({
					message: "Xem file bị lỗi vui lòng gọi hỗ trợ",
					type: "error",
					position: "top-right",
					duration: 3000
				});
			}
		},
		async downloadAssetDocument() {
			let arrayAsset = [];
			if (this.dataPC.real_estate && this.dataPC.real_estate.length > 0) {
				await this.dataPC.real_estate.forEach(item => {
					if (
						item.appraises &&
						item.appraises.appraise_has_assets &&
						item.appraises.appraise_has_assets.length > 0
					) {
						item.appraises.appraise_has_assets.forEach(asset => {
							arrayAsset.push(asset.asset_general_id);
						});
					}
					if (
						item.apartment &&
						item.apartment.apartment_has_assets &&
						item.apartment.apartment_has_assets.length > 0
					) {
						item.apartment.apartment_has_assets.forEach(asset => {
							arrayAsset.push(asset.asset_general_id);
						});
					}
				});
			}
			this.isSubmit = true;
			const res = await WareHouse.getPrint(arrayAsset, this.dataPC.id);
			if (res.data) {
				const file = res.data;
				const fileLink = document.createElement("a");
				fileLink.href = file.url;
				fileLink.setAttribute("download", file.file_name);
				document.body.appendChild(fileLink);
				fileLink.click();
				fileLink.remove();
				window.URL.revokeObjectURL(fileLink);
			} else {
				this.$toast.open({
					message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
					type: "error",
					position: "top-right",
					duration: 3000
				});
			}
		},
		async viewAppendix1() {
			await Certificate.getPrint(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePrint = file.url;
				}
			});
			this.title = "Tài liệu bảng điều chỉnh QSDĐ";
			this.isShowPrint = true;
		},
		async downloadAppendix1() {
			await Certificate.getPrint(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		async viewAppendix2() {
			await Certificate.getPrintAppendix(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePrint = file.url;
				}
			});
			this.title = "Tài liệu bảng điều chỉnh CTXD";
			this.isShowPrint = true;
		},
		async downloadAppendix2() {
			await Certificate.getPrintAppendix(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		async viewAppendix3() {
			await Certificate.getPrintImage(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePrint = file.url;
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
			this.title = "Tài liệu hình ảnh hiện trạng";
			this.isShowPrint = true;
		},
		async downloadAppendix3() {
			await Certificate.getPrintImage(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		downloadOtherFile(file) {
			if (this.exportAction) {
				axios({
					url:
						process.env.API_URL +
						"/api/certificate/other-document/download/" +
						file.id,
					method: "GET",
					responseType: "blob"
				}).then(response => {
					const url = window.URL.createObjectURL(new Blob([response.data]));
					const link = document.createElement("a");
					link.href = url;
					link.setAttribute("download", file.name);
					document.body.appendChild(link);
					link.click();
					window.URL.revokeObjectURL(link);
					this.$toast.open({
						message: `Tải xuống thành công`,
						type: "success",
						position: "top-right",
						duration: 3000
					});
				});
			}
		},
		deleteOtherFile(file, index) {
			this.openModalDelete = true;
			this.indexDelete = index;
			this.id_file_delete = file.id;
		},
		async handleDelete() {
			const res = await File.deleteFileCertificate(this.id_file_delete);
			if (res.data) {
				this.dataPC.other_documents.splice(this.indexDelete, 1);
				// this.files = this.dataPC.files
				this.$toast.open({
					message: "Xóa thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: "error",
					position: "top-right",
					duration: 3000
				});
			}
		},

		openMessage(
			message,
			type = "error",
			position = "top-right",
			duration = 3000
		) {
			this.$toast.open({
				message: message,
				type: type,
				position: position,
				duration: duration
			});
		},
		checkDiffVersion() {
			let message = "";
			if (this.config.check_version && this.checkVersion.length > 0) {
				message =
					"Sai version. Bạn cần cập nhật lại version trước khi chuyển trạng thái.";
			}
			return message;
		},
		viewAppraiseListVersion() {
			this.isShowAppraiseListVersion = true;
		},
		formatCapitalize(word) {
			return word.toLowerCase().replace(/(?:^|\s|[-"'([{])+\S/g, function(x) {
				return x.toUpperCase();
			});
		},
		setDocumentViewStatus() {
			let isExportAutomatic = true;
			let isCheckRealEstate = true;
			let isCheckConstruction = false;
			let isApartment = false;
			if (this.dataPC.document_type && this.dataPC.document_type.length > 0) {
				if (
					this.dataPC.document_type.filter(function(item) {
						return item !== "DCN" && item !== "DT" && item !== "CC";
					}).length > 0
				) {
					isCheckRealEstate = false;
					isExportAutomatic = false;
				}
				if (
					this.dataPC.document_type.find(i => i === "CC") &&
					(this.dataPC.document_type.find(i => i === "DCN") ||
						this.dataPC.document_type.find(i => i === "DT"))
				) {
					isExportAutomatic = false;
				}
				if (
					this.dataPC.document_type.length === 1 &&
					this.dataPC.document_type.find(i => i === "CC")
				) {
					isApartment = true;
				}
				if (this.dataPC.document_type.find(i => i === "DCN")) {
					isCheckConstruction = true;
				}
			} else {
				isCheckRealEstate = false;
				isExportAutomatic = false;
			}
			if (isCheckRealEstate) {
				this.documentName = [
					"Chứng thư thẩm định",
					"Báo cáo thẩm định",
					"Bảng điều chỉnh QSDĐ",
					"Bảng điều chỉnh CTXD",
					"Hình ảnh hiện trạng",
					"Phiếu thu thập TSSS"
				];
			} else {
				this.documentName = [
					"Chứng thư thẩm định",
					"Báo cáo thẩm định",
					"Phụ lục kèm theo",
					"Phụ lục kèm theo",
					"Phụ lục kèm theo",
					"Phiếu thu thập TSSS"
				];
			}
			this.isCheckRealEstate = isCheckRealEstate;
			this.isCheckConstruction = isCheckConstruction;
			this.isViewAutomationDocument = isExportAutomatic;
			this.isApartment = isApartment;
		},
		downloadDocumentFile(type) {
			let file = this.dataPC.other_documents.find(i => i.description === type);
			if (file) {
				// this.downloadDocument(file)
				this.downloadOtherFile(file);
			} else
				this.openMessage(
					"Không tìm thấy file cần tải. Vui lòng xem refesh lại trang."
				);
		},
		deletedDocumentFile(type) {
			let file = this.dataPC.other_documents.find(i => i.description === type);
			if (file) {
				this.deleteUploadDocument = true;
				this.id_file_delete = file.id;
			} else this.openMessage("Không tìm thấy file cần xóa.");
		},
		async deleteDocument() {
			await Certificate.deleteDocument(this.id_file_delete).then(resp => {
				const file = resp;
				if (file.data) {
					this.dataPC.other_documents = file.data;
					this.openMessage("Xóa thành công.", "success");
				} else if (file.error) this.openMessage(file.error.message);
				else this.openMessage("Không tìm thấy file.");
			});
		},
		async downloadDocument(file) {
			await Certificate.downloadDocument(file.id).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		handleMenuClick(e) {
			if (e.key === "1") {
				this.exportGYC();
			} else if (e.key === "2") {
				this.exportHDTDG();
			} else if (e.key === "3") {
				this.exportKHTDG();
			} else if (e.key === "4") {
				this.exportBBTL();
			}
		},
		async exportGYC() {
			await Certificate.getPrintGYC(this.dataPC.id, 1).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		async exportHDTDG() {
			await Certificate.getPrintHDTDG(this.dataPC.id, 1).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		async exportBBTL() {
			await Certificate.getPrintBBTL(this.dataPC.id, 1).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		async exportKHTDG() {
			await Certificate.getPrintKHTDG(this.dataPC.id, 1).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		async handlePrint(id) {
			const response = await this.priceEstimateStore.getDataAllStep(id, false);
			if (response.error) {
				this.$toast.open({
					message: `${response.error.message}`,
					type: "error",
					position: "top-right"
				});
			} else {
				this.priceEstimates.assets = [];
				this.priceEstimates.totalLandPrice = 0;
				this.priceEstimates.totalTangibleAssetPrice = 0;
				if (!this.miscVariable.isApartment) {
					if (
						!this.priceEstimates.step_3 ||
						!this.priceEstimates.step_3.total_area ||
						this.priceEstimates.step_3.total_area.length === 0
					) {
						this.$toast.open({
							message:
								"Chưa thể in giá sơ bộ do chưa đủ thông tin. Vui lòng lưu giá trị tài sản trước khi in.",
							type: "error",
							position: "top-right"
						});
					}

					for (
						let index = 0;
						index < this.priceEstimates.step_3.total_area.length;
						index++
					) {
						const element = this.priceEstimates.step_3.total_area[index];
						const temp = {
							description: "Phần diện tích PHQH",
							land_type_description: element.land_type_purpose
								? element.land_type_purpose.acronym
								: "",
							area: element.main_area,
							price: element.unit_price,
							total: element.total_price
						};
						this.priceEstimates.totalLandPrice += Number(element.total_price);
						this.priceEstimates.assets.push(temp);
					}

					if (
						this.priceEstimates.step_3.planning_area &&
						this.priceEstimates.step_3.planning_area.length > 0
					) {
						for (
							let index = 0;
							index < this.priceEstimates.step_3.planning_area.length;
							index++
						) {
							const element = this.priceEstimates.step_3.planning_area[index];
							const temp = {
								description: "Phần diện tích không PHQH",
								land_type_description: element.land_type_purpose
									? element.land_type_purpose.acronym
									: "",
								area: element.planning_area,
								price: element.unit_price,
								total: element.total_price
							};
							this.priceEstimates.totalLandPrice += Number(element.total_price);
							this.priceEstimates.assets.push(temp);
						}
					}
					if (
						this.priceEstimates.step_3.tangible_assets &&
						this.priceEstimates.step_3.tangible_assets.length > 0
					)
						this.priceEstimates.totalTangibleAssetPrice = this.priceEstimates.step_3.tangible_assets.reduce(
							(total, asset) =>
								total + (asset.total_price ? Number(asset.total_price) : 0),
							0
						);
				}

				this.priceEstimates.totalAllPrice =
					Number(this.priceEstimates.totalLandPrice) +
					Number(this.priceEstimates.totalTangibleAssetPrice);

				if (!this.isMobile) {
					this.openPrint = true;
				} else {
					this.showLoadingPrint = true;
					await this.pdfgen();
				}
			}

			// this.printEstimateAssetPrice();
		},
		async pdfgen() {
			try {
				const pdfMake = require("pdfmake/build/pdfmake.js");
				const PdfRes = require("@/assets/resources/pdf-images").default;
				let listAssets = [];
				let listTangibleAssets = [];
				let listApartments = [];
				let listPlanningImage = [];
				let totalPriceAsset = 0;
				let totalTangibleAsset = 0;
				let totalApartment = 0;

				if (
					!this.isApartment &&
					this.priceEstimates.assets &&
					this.priceEstimates.assets.length > 0
				) {
					for (
						let index = 0;
						index < this.priceEstimates.assets.length;
						index++
					) {
						const element = this.priceEstimates.assets[index];
						totalPriceAsset += Number(element.total);
						listAssets.push([
							{
								text: element.description,
								fontSize: 9,
								alignment: "left",
								border: [false, false, false, false],
								margin: [0, 5, 0, 5],
								fillColor: "#F7F7F7"
							},
							{
								text: element.land_type_description,
								fontSize: 9,
								alignment: "center",
								border: [false, false, false, false],
								margin: [0, 5, 0, 5],
								fillColor: "#F7F7F7"
							},
							{
								text: this.formatNumber(element.area),
								fontSize: 9,
								alignment: "center",
								border: [false, false, false, false],
								margin: [0, 5, 0, 5],
								fillColor: "#F7F7F7"
							},
							{
								text: this.formatNumber(element.price),
								fontSize: 9,
								alignment: "center",
								border: [false, false, false, false],
								margin: [0, 5, 0, 5],
								fillColor: "#F7F7F7"
							},
							{
								text: this.formatNumber(element.total),
								fontSize: 9,
								alignment: "right",
								border: [false, false, false, false],
								margin: [0, 5, 0, 5],
								fillColor: "#F7F7F7"
							}
						]);
					}
				}
				if (
					!this.isApartment &&
					this.priceEstimates.step_3.tangible_assets &&
					this.priceEstimates.step_3.tangible_assets.length > 0
				) {
					for (
						let index = 0;
						index < this.priceEstimates.step_3.tangible_assets.length;
						index++
					) {
						const element = this.priceEstimates.step_3.tangible_assets[index];
						totalTangibleAsset += Number(element.total_price);
						listTangibleAssets.push([
							{
								text: element.building_type
									? this.formatSentenceCase(element.building_type.description)
									: "",
								fontSize: 9,
								alignment: "left",
								border: [false, false, false, false],
								margin: [0, 5, 0, 5],
								fillColor: "#F7F7F7"
							},
							{
								text: this.formatArea(element.total_construction_area),
								fontSize: 9,
								alignment: "center",
								border: [false, false, false, false],
								margin: [0, 5, 0, 5],
								fillColor: "#F7F7F7"
							},
							{
								text: this.formatNumber(element.unit_price),
								fontSize: 9,
								alignment: "center",
								border: [false, false, false, false],
								margin: [0, 5, 0, 5],
								fillColor: "#F7F7F7"
							},
							{
								text: element.remaining_quality,
								fontSize: 9,
								alignment: "center",
								border: [false, false, false, false],
								margin: [0, 5, 0, 5],
								fillColor: "#F7F7F7"
							},
							{
								text: this.formatNumber(element.total_price),
								fontSize: 9,
								alignment: "right",
								border: [false, false, false, false],
								margin: [0, 5, 0, 5],
								fillColor: "#F7F7F7"
							}
						]);
					}
				}
				if (
					this.isApartment &&
					this.priceEstimates.step_3.apartment_finals &&
					this.priceEstimates.step_3.apartment_finals.length > 0
				) {
					for (
						let index = 0;
						index < this.priceEstimates.step_3.apartment_finals.length;
						index++
					) {
						const element = this.priceEstimates.step_3.apartment_finals[index];
						totalApartment += Number(element.total_price);
						listApartments.push([
							{
								text: element.name,
								fontSize: 9,
								alignment: "left",
								border: [false, false, false, false],
								margin: [0, 5, 0, 5],
								fillColor: "#F7F7F7"
							},
							{
								text: this.formatNumber(element.unit_price),
								fontSize: 9,
								alignment: "center",
								border: [false, false, false, false],
								margin: [0, 5, 0, 5],
								fillColor: "#F7F7F7"
							},
							{
								text: this.formatNumber(element.total_price),
								fontSize: 9,
								alignment: "right",
								border: [false, false, false, false],
								margin: [0, 5, 0, 5],
								fillColor: "#F7F7F7"
							}
						]);
					}
					<div
						class="card mb-4"
						v-if="
							isApartment &&
								data.step_3.apartment_finals &&
								data.step_3.apartment_finals.length > 0
						"
					>
						<table cellspacing="0" cellpadding="0">
							<thead class="vendorListHeadingResult p-0">
								<tr>
									<th style="border-top-left-radius: 15px;" class="text-left">
										Tên tài sản
									</th>
									<th class="text-center">
										Diện tích (m<sup style="font-size: 9px !important">2</sup>)
									</th>
									<th class="text-center">Đơn giá (đ)</th>
									<th style="border-top-right-radius: 15px;" class="text-right">
										Thành tiền (đ)
									</th>
								</tr>
							</thead>
						</table>
					</div>;
				}
				if (
					this.priceEstimates.step_3.image_planning_info &&
					this.priceEstimates.step_3.image_planning_info.length > 0
				) {
					for (
						let index = 0;
						index < this.priceEstimates.step_3.image_planning_info.length;
						index++
					) {
						const element = this.priceEstimates.step_3.image_planning_info[
							index
						];
						listPlanningImage.push({
							image: await this.getBase64ImageFromURL(element.link),
							fit: [400, 200],
							alignment: "center",
							// height: 200,
							// width: 600,
							margin: [0, 5, 0, 0]
						});
					}
				}
				const info1 = [
					{
						text: [
							{
								text: "Mã sơ bộ: ",
								fontSize: 9
							},
							{
								text: "TSSB_" + this.priceEstimates.id,
								fontSize: 9,
								bold: true
							}
						],
						alignment: "right"
					},
					{
						layout: {
							hLineColor: i => "#0685B2",
							vLineWidth: i => 1.5,
							hLineWidth: i => 1.5
						},
						alignment: "left",
						table: {
							widths: ["*"],
							body: [
								[
									{
										text: "THÔNG TIN VỀ NGƯỜI YÊU CẦU",
										border: [false, false, false, true],
										bold: true,
										fontSize: 11,
										color: "#0685B2",
										margin: [-5, 0, 0, 0]
									}
								]
							]
						}
					},
					{
						layout: {
							hLineColor: i => "#0685B2",
							vLineWidth: i => 0,
							hLineWidth: i => 0
						},
						alignment: "left",
						table: {
							widths: [90, "*"],
							body: [
								[
									{
										text: "Tên người yêu cầu: ",
										border: [false, false, false, false],
										bold: false,
										fontSize: 9,
										margin: [-5, 3, 0, 3]
									},
									{
										text: this.priceEstimates.step_3.petitioner_name
											? this.priceEstimates.step_3.petitioner_name.toUpperCase()
											: "",
										border: [false, false, false, false],
										bold: true,
										fontSize: 9,
										margin: [0, 3, 0, 3]
									}
								],
								[
									{
										text: "Ngày yêu cầu: ",
										border: [false, false, false, false],
										bold: false,
										fontSize: 9,
										margin: [-5, 0, 0, 3]
									},
									{
										text: this.priceEstimates.step_3.request_date,
										border: [false, false, false, false],
										bold: true,
										fontSize: 9,
										margin: [0, 0, 0, 3]
									}
								],
								[
									{
										text: "Mục đích thẩm định: ",
										border: [false, false, false, false],
										bold: false,
										fontSize: 9,
										margin: [-5, 0, 0, 3]
									},
									{
										text: this.priceEstimates.step_3.appraise_purpose
											? this.priceEstimates.step_3.appraise_purpose.name
											: "",
										border: [false, false, false, false],
										bold: true,
										fontSize: 9,
										margin: [0, 0, 0, 3]
									}
								]
							]
						}
					},
					{
						layout: {
							hLineColor: i => "#0685B2",
							vLineWidth: i => 1.5,
							hLineWidth: i => 1.5
						},
						alignment: "left",
						table: {
							widths: ["*"],
							body: [
								[
									{
										text: "THÔNG TIN SƠ BỘ VỀ TÀI SẢN",
										border: [false, false, false, true],
										bold: true,
										fontSize: 11,
										color: "#0685B2",
										margin: [-5, 0, 0, 0]
									}
								]
							]
						}
					},
					{
						layout: {
							hLineColor: i => "#0685B2",
							vLineWidth: i => 0,
							hLineWidth: i => 0
						},
						alignment: "left",
						table: {
							widths: [90, "*"],
							body: [
								[
									{
										text: "Loại tài sản: ",
										border: [false, false, false, false],
										bold: false,
										fontSize: 9,
										margin: [-5, 3, 0, 3]
									},
									{
										text: this.priceEstimates.step_1.general_infomation
											.asset_type
											? this.formatSentenceCase(
													this.priceEstimates.step_1.general_infomation
														.asset_type.description
											  )
											: "",
										border: [false, false, false, false],
										bold: true,
										fontSize: 9,
										margin: [0, 3, 0, 3]
									}
								],
								[
									{
										text: "Tên tài sản: ",
										border: [false, false, false, false],
										bold: false,
										fontSize: 9,
										margin: [-5, 0, 0, 3]
									},
									{
										text: this.priceEstimates.step_1.general_infomation
											.appraise_asset
											? this.priceEstimates.step_1.general_infomation
													.appraise_asset
											: "",
										border: [false, false, false, false],
										bold: true,
										fontSize: 9,
										margin: [0, 0, 0, 3]
									}
								],
								[
									{
										text: "Địa chỉ tài sản: ",
										border: [false, false, false, false],
										bold: false,
										fontSize: 9,
										margin: [-5, 0, 0, 3]
									},
									{
										text: this.priceEstimates.step_1.general_infomation
											.full_address_street
											? this.priceEstimates.step_1.general_infomation
													.full_address_street
											: "",
										border: [false, false, false, false],
										bold: true,
										fontSize: 9,
										margin: [0, 0, 0, 3]
									}
								],
								[
									{
										text: "Tọa độ: ",
										border: [false, false, false, false],
										bold: false,
										fontSize: 9,
										margin: [-5, 0, 0, 3]
									},
									{
										text: this.priceEstimates.step_1.general_infomation
											.coordinates
											? this.priceEstimates.step_1.general_infomation
													.coordinates
											: "",
										border: [false, false, false, false],
										bold: true,
										fontSize: 9,
										margin: [0, 0, 0, 3]
									}
								],
								[
									{
										text: "Mô tả vị trí: ",
										border: [false, false, false, false],
										bold: false,
										fontSize: 9,
										margin: [-5, 0, 0, 3]
									},
									{
										text:
											!this.isApartment &&
											this.priceEstimates.step_1.traffic_infomation.description
												? this.priceEstimates.step_1.traffic_infomation
														.description
												: this.priceEstimates.step_1.apartment_properties
														.description
												? this.priceEstimates.step_1.apartment_properties
														.description
												: "",
										border: [false, false, false, false],
										bold: true,
										fontSize: 9,
										margin: [0, 0, 0, 3]
									}
								]
							]
						}
					},
					{
						layout: {
							hLineColor: i => "#0685B2",
							vLineWidth: i => 1.5,
							hLineWidth: i => 1.5
						},
						alignment: "left",
						table: {
							widths: ["*"],
							body: [
								[
									{
										text: "KẾT QUẢ ƯỚC TÍNH SƠ BỘ",
										border: [false, false, false, true],
										bold: true,
										fontSize: 11,
										color: "#0685B2",
										margin: [-5, 0, 0, 0]
									}
								]
							]
						}
					},
					listAssets.length > 0
						? {
								layout: {
									hLineColor: i => "white",
									vLineWidth: i => 1,
									hLineWidth: i => 1
								},
								table: {
									widths: [200, 50, 70, 80, 88],
									body: [
										[
											{
												text: "Quyền sử dụng đất",
												fontSize: 9,
												bold: true,
												alignment: "left",
												border: [false, false, false, true],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "MĐSD",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, true],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "Diện tích (m²)",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, true],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "Đơn giá (đ)",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, true],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "Thành tiền (đ)",
												fontSize: 9,
												bold: true,
												alignment: "right",
												border: [false, false, false, true],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											}
										],
										...listAssets,
										[
											{
												text: "TỔNG CỘNG",
												fontSize: 9,
												bold: true,
												alignment: "left",
												border: [false, false, false, false],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, false],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, false],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, false],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: this.formatNumber(totalPriceAsset),
												fontSize: 9,
												bold: true,
												alignment: "right",
												border: [false, false, false, false],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											}
										]
									]
								},
								margin: [0, 15, 0, 0]
						  }
						: {},
					listTangibleAssets.length > 0
						? {
								layout: {
									hLineColor: i => "white",
									vLineWidth: i => 1,
									hLineWidth: i => 1
								},
								table: {
									widths: [150, 100, 70, 80, 88],
									body: [
										[
											{
												text: "Công trình xây dựng",
												fontSize: 9,
												bold: true,
												alignment: "left",
												border: [false, false, false, true],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "Diện tích sàn (m²)",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, true],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "Đơn giá (đ)",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, true],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "CLCL (%)",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, true],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "Thành tiền (đ)",
												fontSize: 9,
												bold: true,
												alignment: "right",
												border: [false, false, false, true],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											}
										],
										...listTangibleAssets,
										[
											{
												text: "TỔNG CỘNG",
												fontSize: 9,
												bold: true,
												alignment: "left",
												border: [false, false, false, false],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, false],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, false],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, false],
												margin: [0, 3, 0, 0],
												fillColor: "#F7F7F7"
											},
											{
												text: this.formatNumber(totalTangibleAsset),
												fontSize: 9,
												bold: true,
												alignment: "right",
												border: [false, false, false, false],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											}
										]
									]
								},
								margin: [0, 15, 0, 0]
						  }
						: {},
					listApartments.length > 0
						? {
								layout: {
									hLineColor: i => "white",
									vLineWidth: i => 1,
									hLineWidth: i => 1
								},
								table: {
									widths: [300, 100, 105],
									body: [
										[
											{
												text: "Tên tài sản",
												fontSize: 9,
												bold: true,
												alignment: "left",
												border: [false, false, false, true],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "Đơn giá (m²)",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, true],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},

											{
												text: "Thành tiền (đ)",
												fontSize: 9,
												bold: true,
												alignment: "right",
												border: [false, false, false, true],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											}
										],
										...listApartments
										// [
										// 	{
										// 		text: "TỔNG CỘNG",
										// 		fontSize: 9,
										// 		bold: true,
										// 		alignment: "left",
										// 		border: [false, false, false, false],
										// 		margin: [0, 5, 0, 5],
										// 		fillColor: "#F7F7F7"
										// 	},

										// 	{
										// 		text: "",
										// 		fontSize: 9,
										// 		bold: true,
										// 		alignment: "center",
										// 		border: [false, false, false, false],
										// 		margin: [0, 3, 0, 0],
										// 		fillColor: "#F7F7F7"
										// 	},
										// 	{
										// 		text: this.formatNumber(totalApartment),
										// 		fontSize: 9,
										// 		bold: true,
										// 		alignment: "right",
										// 		border: [false, false, false, false],
										// 		margin: [0, 5, 0, 5],
										// 		fillColor: "#F7F7F7"
										// 	}
										// ]
									]
								},
								margin: [0, 15, 0, 0]
						  }
						: {},

					!this.isApartment
						? {
								layout: {
									hLineColor: i => "white",
									vLineWidth: i => 1,
									hLineWidth: i => 1
								},
								table: {
									widths: [150, 100, 70, 60, 108],
									body: [
										[
											{
												text: "TỔNG CỘNG",
												fontSize: 9,
												bold: true,
												alignment: "left",
												border: [false, false, false, false],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7",
												color: "#0685B2"
											},
											{
												text: "",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, false],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, false],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: "",
												fontSize: 9,
												bold: true,
												alignment: "center",
												border: [false, false, false, false],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7"
											},
											{
												text: this.formatNumber(
													Number(this.priceEstimates.totalAllPrice)
												),
												fontSize: 9,
												bold: true,
												alignment: "right",
												border: [false, false, false, false],
												margin: [0, 5, 0, 5],
												fillColor: "#F7F7F7",
												color: "#0685B2"
											}
										]
									]
								},
								margin: [0, 15, 0, 0]
						  }
						: {},
					{
						text: "",
						pageBreak: "after"
					},
					listPlanningImage.length > 0
						? {
								text: "* Ảnh thông tin quy hoạch: ",
								fontSize: 9,
								bold: true
						  }
						: {},
					listPlanningImage.length > 0 ? [...listPlanningImage] : {},
					{
						margin: [0, 10, 0, 0],
						columns: [
							{
								width: "50%",
								stack: [
									{
										text:
											"* Biên độ chênh lệch : +/- " +
											this.priceEstimates.step_3.difference_amplitude +
											" %",
										fontSize: 9,
										italics: true
									},
									{
										text: "* Ghi chú khác ",
										fontSize: 9,
										italics: true
									},
									{
										text: this.priceEstimates.step_3.note,
										fontSize: 9,
										italics: true,
										alignment: "justify"
									}
								]
							},
							{
								width: "50%",
								margin: [35, 0, 0, 0],
								columns: [
									{
										width: "30%",
										stack: [
											{
												text: "Chữ ký",
												fontSize: 9,
												italics: true
											},
											{
												text: "Người ước tính",
												fontSize: 9,
												italics: true
											},
											{
												text: "Thời điểm",
												fontSize: 9,
												italics: true,
												alignment: "justify"
											}
										]
									},
									{
										width: "5%",
										stack: [
											{
												text: ":",
												fontSize: 9,
												italics: true
											},
											{
												text: ":",
												fontSize: 9,
												italics: true
											},
											{
												text: ":",
												fontSize: 9,
												italics: true,
												alignment: "justify"
											}
										]
									},
									{
										width: "*",
										stack: [
											{
												text: "---------------------",
												fontSize: 9,
												italics: true,
												alignment: "right"
											},
											{
												text: this.priceEstimates.created_by
													? this.priceEstimates.created_by.name
													: "",
												fontSize: 9,
												italics: true,
												alignment: "right"
											},
											{
												text: this.priceEstimates.updated_at
													? this.formatDatePDF(this.priceEstimates.updated_at)
													: "",
												fontSize: 9,
												italics: true,
												alignment: "right"
											},
											{
												text: " ",
												fontSize: 9,
												italics: true,
												alignment: "right",
												margin: [0, 50, 0, 0]
											},
											{
												text: "ĐẠI DIỆN PHÁP LUẬT",
												fontSize: 9,
												bold: true,
												alignment: "center",
												margin: [0, 0, 0, 0]
											},
											{
												text: " ",
												fontSize: 9,
												italics: true,
												alignment: "right",
												margin: [0, 30, 0, 0]
											},
											{
												text: "Huỳnh Văn Ngoãn",
												fontSize: 9,
												bold: true,
												alignment: "center",
												margin: [0, 0, 0, 0]
											}
										]
									}
								]
							}
						]
					}
				];

				pdfMake.vfs = pdfFonts.pdfMake.vfs;
				pdfMake.fonts = {
					Opensans: {
						normal: "Opensans-Regular.ttf",
						bold: "Opensans-Bold.ttf",
						italics: "Opensans-Italic.ttf",
						bolditalics: "Opensans-BoldItalic.ttf"
					}
				};
				const docDefinition = {
					info: {
						title: "Phiếu tài sản sơ bộ",
						author: "",
						subject: "",
						keywords: ""
					},
					pageMargins: [30, 130, 30, 100],

					footer: (currentPage, pageCount, pageSize) => {
						return [
							{
								widths: ["*"],
								margin: [31, 0, 31],
								layout: {
									hLineColor: i => (i === 0 ? "white" : ""),
									vLineWidth: i => 0,
									hLineWidth: i => (i === 0 ? 1 : 0)
								},
								table: {
									body: [
										[
											[
												{},
												{
													text: currentPage + "/" + pageCount,
													fontSize: 9,
													margin: [0, 210, 0, 0],
													alignment: "center"
												},
												{}
											]
										]
									]
								}
							}
						];
					},
					header: (currentPage, pageCount, pageSize) => {
						return [
							{
								image: "imageHeader",
								width: 600,
								height: 120
							}
						];
					},

					content: info1,

					images: {
						imageHeader: PdfRes.image
					},
					styles: {},
					defaultStyle: {
						font: "Opensans"
					}
				};
				const title =
					"TSSB_" +
					this.priceEstimates.id +
					(this.priceEstimates.step_3.petitioner_name
						? "_" +
						  this.priceEstimates.step_3.petitioner_name.replaceAll(" ", "_")
						: "") +
					(this.priceEstimates.createdAtString
						? "_" + this.priceEstimates.createdAtString
						: "");

				const pdfDocGenerator = await pdfMake
					.createPdf(docDefinition)
					.download(title);
				this.showLoadingPrint = false;
			} catch (e) {
				console.log(e);
				this.showLoadingPrint = false;
			}
			// pdfDocGenerator.getDataUrl(dataUrl => {
			// 	this.pdfsrc = dataUrl;
			// 	let blobUrl = null;
			// 	fetch(dataUrl)
			// 		.then(res => res.blob())
			// 		.then(URL.createObjectURL)
			// 		.then(ret => {
			// 			blobUrl = ret;
			// 			this.pdfsrc = blobUrl || dataUrl;
			// 			return blobUrl;
			// 		})
			// 		.then(console.log);
			// });
		},
		getBase64ImageFromURL(url) {
			if (!url) return "";
			return new Promise((resolve, reject) => {
				const img = new Image();
				img.setAttribute("crossOrigin", "anonymous");
				img.onload = () => {
					const canvas = document.createElement("canvas");
					canvas.width = img.width;
					canvas.height = img.height;

					const ctx = canvas.getContext("2d");
					ctx.drawImage(img, 0, 0);

					const dataURL = canvas.toDataURL("image/png");

					resolve(dataURL);
				};

				img.onerror = error => {
					reject(error);
				};

				img.src = url + "?r=" + Math.floor(Math.random() * 100000);
			});
		},
		formatArea(value) {
			let num = (value / 1).toFixed(2).replace(",", ".");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		}
	},
	beforeMount() {
		this.setDocumentViewStatus();
	},
	async mounted() {
		this.checkVersion = this.checkVersion2;
	},
	watch: {
		"dataPC.document_type": {
			deep: true,
			handler(newValue) {
				this.setDocumentViewStatus();
			}
		}
	}
};
</script>
<style scoped lang="scss">
.div_radio {
	margin-bottom: 0.5rem;
}
.dataPC-map {
	height: 100%;
	flex: 1;
}

.card {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 1rem;

	&-footer {
		padding: 15px 24px;
	}

	&-title {
		padding: 20px 25px 10px;
		margin-bottom: 0;
		color: #e8e8e8;
		border-bottom: 2px solid;
		&__img {
			padding: 8px 20px;
		}
		h3 {
			color: #007ec6;
		}
		@media (max-width: 768px) {
			padding: 12px;
		}

		.title {
			font-size: 1.125rem;
			font-weight: 700;
			margin-bottom: 0;
		}
	}

	&-body {
		@media (max-width: 787px) {
			padding: 15px;
		}
	}

	&-info {
		.title {
			font-size: 1.125rem;
			font-weight: 700;
			margin-top: 28px;

			&-highlight {
				background: rgba(252, 194, 114, 0.53);
				text-align: center;
				padding: 10px 0;
				border-radius: 2px;
			}
		}
	}

	&-land {
		position: relative;
		padding: 0;
	}
}
.card-status-pre-certificate {
	border-radius: 5px;
	background: #ffffff;
	margin-bottom: 10px;
	font-weight: 600;
	padding: 10px;
	font-size: 16px !important;
	border: 1px solid #000000;
	@media (max-width: 768px) {
		margin-bottom: 10px;
	}

	@media (max-width: 418px) {
		margin-bottom: 10px;
	}
}
.card-status-certificate {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 10px;
	font-weight: 600;
	padding: 10px;
	font-size: 16px !important;
	color: darkgray;
	cursor: pointer;

	@media (max-width: 768px) {
		margin-bottom: 10px;
	}

	@media (max-width: 418px) {
		margin-bottom: 10px;
	}
}

.dataPC-group-container {
	margin-top: 10px;
}

.color-black {
	color: #333333;
}

.img_document_action {
	width: 2rem;
	height: 2rem;
	cursor: pointer;
	background: #ffffff;
	min-width: 1.5rem;
	min-height: 1.5rem;
}
.btn-edit {
	cursor: pointer;
	display: flex;
	border-radius: 5.88235px;
	align-items: end;
	img {
		width: 20px;
		height: 14px;
		height: auto;
	}
}
.btn {
	&-orange {
		background: #faa831;
		text-align: center;
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		height: 35px;
		// width: 100px;
		color: #fff;
		// margin: 15px 0 0;
		box-sizing: border-box;

		&:hover {
			border-color: #dc8300;
		}
	}
}
.btn-upload {
	left: 0;
	opacity: 0;
	width: 100%;
	min-height: 10rem;
	cursor: pointer;
	// position: absolute;
}
.btn-upload-mini {
	left: 0;
	opacity: 0;
	width: 2rem;
	// min-height: 10rem;
	cursor: pointer;
	// position: absolute;
	padding: 0;
	border: 0;
}
.btn_list_appraise {
	&-orange {
		background: #faa831;
		text-align: center;
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		height: 35px;
		min-width: 150px;
		color: #fff;
		box-sizing: border-box;

		&:hover {
			border-color: #dc8300;
		}
	}
}

.img-dropdown {
	cursor: pointer;
	width: 18px;

	&__hide {
		transform: rotate(90deg);
		transition: 0.3s;
	}
}

.img-locate {
	cursor: pointer;
	position: absolute;
	right: 14px;
	top: 2.1rem;
	background-color: #f5f5f5;
	height: 2.1rem;
	width: 32px;
	display: grid;
	place-items: center;

	img {
		height: 60%;
	}
}

.text-error {
	color: #cd201f;
}

.select-group {
	background-color: #f6f7fb;
	border: 1px solid #e8e8e8;
	border-radius: 3px;
	padding: 16px 22px;

	.select-title {
		color: #00507c;
		font-weight: 700;
		white-space: nowrap;
		margin-bottom: unset !important;
	}
}

.content_form {
	padding-left: 0.75rem;
}
.border_disable {
	border-color: #d9d9d9 !important;
}
.detail_pre_certification {
	// padding: 0 1rem;
	margin-bottom: 60px;
	@media (max-width: 449px) {
		margin-bottom: 120px;
	}
}
.detail_certificate_1 {
	padding: 0.75rem;
	border-radius: 5px;
	border: 1px solid #b5e5ff;
	background-color: #eef9ff;
}
.detail_certificate_2 {
	padding: 0.75rem;
	border-radius: 5px;
	border: 1px solid #e8e8e8;
	background-color: #f6f7fb;
}

.detail_certificate_3 {
	height: inherit;
	padding: 0.75rem;
	border-radius: 5px;
	border: 1px solid #e8e8e8;
	background-color: #f6f7fb;
}
.margin_content_inline {
	margin-right: 10px;
}
.container_content {
	min-height: 20px;
	p {
		margin-bottom: unset !important;
	}
}

.arrowBox {
	margin-top: -10px;
	position: relative;
	background: #fbaf1c;
	height: 22px;
	line-height: 22px;
	text-align: center;
	color: #fff;
	font-weight: 600;
	font-size: 16px !important;
	display: inline-block;
	cursor: pointer;
	padding: 0 5px 0 0;
}
.arrow-right:after {
	content: "";
	position: absolute;
	right: -11px;
	top: 0;
	border-top: 11px solid transparent;
	border-bottom: 11px solid transparent;
	border-left: 11px solid #fbaf1c;
}

.btn {
	&-history {
		position: fixed;
		right: 0;
		top: 210px;
		z-index: 100;
		border-radius: 5px 0 0 5px;
		padding: 0.5rem 0.3rem;
	}
}
.input_download_certificate {
	position: relative;
	border: 1px solid #b6d5f3;
	border-radius: 5px;
	height: 3.85rem;
	padding: 0.85rem 0px;
}
.title_input_download {
	color: #00507c;
	font-weight: 600;
}
.img_input_download {
	margin-right: 10px;
	max-width: 2rem;
}

.title_input_content {
	font-size: 18px;
}
.input_upload_file {
	background-image: repeating-linear-gradient(
			0deg,
			#b6d5f3,
			#b6d5f3 10px,
			transparent 10px,
			transparent 20px,
			#b6d5f3 20px
		),
		repeating-linear-gradient(
			90deg,
			#b6d5f3,
			#b6d5f3 10px,
			transparent 10px,
			transparent 20px,
			#b6d5f3 20px
		),
		repeating-linear-gradient(
			180deg,
			#b6d5f3,
			#b6d5f3 10px,
			transparent 10px,
			transparent 20px,
			#b6d5f3 20px
		),
		repeating-linear-gradient(
			270deg,
			#b6d5f3,
			#b6d5f3 10px,
			transparent 10px,
			transparent 20px,
			#b6d5f3 20px
		);
	background-size: 2px 100%, 100% 2px, 2px 100%, 100% 2px;
	background-position: 0 0, 0 0, 100% 0, 0 100%;
	background-repeat: no-repeat;
	// border: dotted 1px solid #B6D5F3;
	cursor: pointer;
	min-height: 8rem;
	border-radius: 5px;
}
.img-upload {
	margin-left: 20px;
	position: relative;
	width: 123px;
	height: 35px;
	color: #fff;
	background: #faa831;
	font-size: 1.125rem;
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	display: flex;
	justify-content: center;
	align-items: center;
	box-sizing: border-box;
	cursor: pointer;
	input {
		cursor: pointer !important;
		position: absolute;
		top: 0;
		left: 0;
		bottom: 0;
		width: 100%;
		opacity: 0;
	}
}
.table-wrapper {
	.ant-table-filter-dropdown-btns {
		background-color: white !important;
	}

	.ant-table-filter-dropdown-link.confirm {
		color: red;
	}

	/deep/ .ant-table-thead > tr > th {
		font-weight: 700 !important;
		background-color: #dee6ee !important;
		color: #3d4d65 !important;
		// border-right: 1px solid white !important;
	}
	/deep/ .ant-table-wrapper .ant-spin-container .ant-table {
		border: 1px solid #dee6ee;
	}

	/deep/ .ant-table-column-title {
		color: #00507c;
	}

	/deep/ .table-striped td {
		background-color: #f6f7fb;
		border-color: #dee6ee;
		border-width: 0;
	}

	/deep/ .ant-table-tbody,
	/deep/ .ant-table-body {
		box-shadow: none;
		min-height: 10vh;
	}
	/deep/ .ant-table-pagination {
		display: none;
	}

	.pagination-wrapper {
		margin-top: 16px;
		display: flex;
		justify-content: space-between;
		align-items: center;

		.ant-select {
			margin-left: 11px;
			margin-right: 11px;
		}

		.page-size {
			display: flex;
			align-items: center;
			margin-right: 20px;
		}

		.ant-pagination {
			flex-grow: 1;
			display: flex;
			align-items: center;
			justify-content: center;
			flex-wrap: wrap;
			row-gap: 10px;

			/deep/ .ant-pagination-total-text {
				height: unset;
				flex-grow: 1;

				@media (max-width: 1024px) {
					width: 100%;
					text-align: center;
					margin-bottom: 20px;
				}
			}

			/deep/ .ant-pagination-item-active {
				background: #007ec6;

				a {
					color: #ffffff;
				}
			}

			/deep/ .ant-pagination-prev,
			/deep/ .ant-pagination-next {
				border: 1px solid #d9d9d9;

				&:hover {
					border-color: #1890ff;
					transition: all 0.3s;
				}

				a:hover {
					i {
						color: #1890ff;
					}
				}
			}
		}

		@media (max-width: 1024px) {
			flex-direction: column;
			gap: 20px;
		}
	}
}

.dot-image {
	width: 2em;
	border-radius: 2em;
	height: 2em;
	object-fit: cover;
}
/deep/ .ant-timeline-item-content {
	margin-left: 25px;
	p {
		margin-bottom: 0.2em;
	}
}
/deep/ .ant-timeline-item-tail {
	border-left: 2px solid #26bf5fad;
}
.text-none {
	text-transform: none !important;
}
.btn_group {
	@media (max-width: 767px) {
		width: 100%;
	}
	/deep/ .btn-secondary {
		background-image: none;
		border-color: none !important;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
	}
}
.container_total {
	margin-left: 2rem;
	padding: 6px;
	border: 1px solid;
	color: #007ec6;
	font-weight: 600;
	border-radius: 5px;
	background-color: #eef9ff;
}
.total {
	color: #007ec6;
	font-weight: 700;
	font-size: 1.2rem;
}
.full-address {
	width: 200px;
	white-space: nowrap;
	-webkit-line-clamp: 2 !important;
	overflow: hidden;
	text-overflow: ellipsis;
	margin-bottom: 0;
	text-transform: none;

	&:first-letter {
		text-transform: none;
	}
}
.link-detail {
	white-space: nowrap;
	text-transform: uppercase;
	background: transparent;
	border: none;
	cursor: pointer;
	&:hover,
	&:focus,
	&:active {
		color: #faa831;
		border: none;
		outline: none;
	}
}
.link-detail-mobile {
	white-space: nowrap;
	text-transform: uppercase;
	background: transparent;
	border: none;
}
.btn_dropdown {
	border: white;
	border-radius: 5px;
	height: 35px;
	@media (max-width: 767px) {
		margin-top: 10px;
	}
}
.infor-box {
	padding: 1rem;
	border-radius: 12px 15px;
	background-color: #eef9ff;
	border: 1px solid #007ec6;
	color: #446b92;
	@media (max-height: 660px) {
		font-size: 12px;
	}
	@media (max-height: 970px) and (min-height: 660px) {
	}
	.row_hidden {
		visibility: hidden;
	}
}
.cursor_pointer {
	cursor: pointer;
}
.title_color {
	color: lightgray;
}
.img_filter {
	filter: grayscale(100%) invert(100%);
}
.btn-export {
	border: 1px solid black;
	color: black;
	margin-top: 8px;
	margin-left: 5px;
	height: 45px;
}
</style>
