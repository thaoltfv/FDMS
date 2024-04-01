<template>
	<div
		class="detail_certification_brief row"
		:style="isMobile() ? { margin: '0' } : {}"
	>
		<div class="col-12" :style="isMobile() ? { padding: '0' } : {}">
			<div class="card">
				<div class="card-title">
					<div class="d-flex justify-content-between align-items-center">
						<h3 class="title">Thông tin chung</h3>
						<div class="row" style="display: flex; align-items: center">
							<div class="color_content card-status">
								{{ idData ? `HSTD_${idData}` : "HSTD" }} |
								<span>{{ statusDescription }}</span>
							</div>
							<a-dropdown>
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
							</a-dropdown>
							<div
								v-if="form.pre_certificate_id"
								id="pre_certificate_id"
								@click="handleDetailPreCertificate(form.pre_certificate_id)"
								class="mr-4 arrowBox arrow-right"
							>
								<icon-base
									name="nav_ycsb"
									width="20px"
									height="20px"
									class="item-icon svg-inline--fa"
								/>
								{{ `YCSB_${form.pre_certificate_id}` }}
								<b-tooltip target="pre_certificate_id" placement="top-right">{{
									`Được chuyển tiếp từ YCSB_${form.pre_certificate_id}`
								}}</b-tooltip>
							</div>
						</div>
					</div>
				</div>
				<div class="card-body card-info" v-show="showCardDetailAppraise">
					<div class="row justify-content-between">
						<div class="col-md-12 col-lg-6 mt-1">
							<div class="detail_certificate_1 h-100">
								<div class="d-flex container_content justify-content-between">
									<div class="d-flex container_content">
										<strong class="margin_content_inline">Khách hàng:</strong>
										<p>{{ form.petitioner_name }}</p>
									</div>
									<div
										v-if="edit && (editInfo || editDocument)"
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
									<p>{{ form.petitioner_identity_card }}</p>
									<strong class="margin_content_inline">Điện thoại:</strong>
									<p>{{ form.petitioner_phone }}</p>
								</div>
								<!-- <div class="d-flex container_content">
											<strong class="margin_content_inline">Điện thoại:</strong> <p>{{form.petitioner_phone}}</p>
										</div> -->
								<div class="d-flex container_content">
									<strong class="margin_content_inline">Địa chỉ:</strong>
									<p>{{ form.petitioner_address }}</p>
								</div>
								<div class="d-flex container_content">
									<strong class="margin_content_inline"
										>Mục đích thẩm định:</strong
									>
									<p>
										{{
											form.appraise_purpose ? form.appraise_purpose.name : ""
										}}
									</p>
								</div>
								<div class="d-flex container_content">
									<strong class="margin_content_inline"
										>Thời điểm thẩm định:</strong
									>
									<p>
										{{
											form.appraise_date ? formatDate(form.appraise_date) : ""
										}}
									</p>
								</div>
								<div class="d-flex container_content">
									<strong class="margin_content_inline">Hợp đồng:</strong>
									<p class="margin_content_inline">
										Số: {{ form.document_num }}
									</p>
									<p>
										Ngày:
										{{
											form.document_date ? formatDate(form.document_date) : ""
										}}
									</p>
								</div>
								<div class="d-flex container_content">
									<strong class="margin_content_inline">Chứng thư:</strong>
									<p class="margin_content_inline">
										Số: {{ form.certificate_num }}
									</p>
									<p>
										Ngày:
										{{
											form.certificate_date
												? formatDate(form.certificate_date)
												: ""
										}}
									</p>
								</div>
								<div class="d-flex container_content">
									<strong class="margin_content_inline"
										>Tổng phí dịch vụ:</strong
									>
									<p>
										{{ form.service_fee ? formatNumber(form.service_fee) : 0 }}đ
									</p>
								</div>
								<div class="d-flex container_content">
									<strong class="margin_content_inline">Chiết khấu:</strong>
									<p>{{ form.commission_fee ? form.commission_fee : 0 }}%</p>
								</div>
								<div class="d-flex container_content">
									<strong class="margin_content_inline">Ghi chú:</strong
									><span id="note" class="text-left">{{
										form.note && form.note.length > 25
											? form.note.substring(25, 0) + "..."
											: form.note
									}}</span>
									<b-tooltip target="note" placement="top-right">{{
										form.note
									}}</b-tooltip>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-lg-6 mt-1">
							<div class="row">
								<div class="col-12">
									<div class="detail_certificate_2">
										<div class="d-flex container_content">
											<strong class="margin_content_inline">Đối tác:</strong>
											<p>{{ form.customer ? form.customer.name : "" }}</p>
										</div>
										<div class="d-flex container_content">
											<strong class="margin_content_inline">Địa chỉ:</strong>
											<p>{{ form.customer ? form.customer.address : "" }}</p>
										</div>
										<div class="d-flex container_content">
											<strong class="margin_content_inline">Liên hệ:</strong>
											<p>{{ form.customer ? form.customer.phone : "" }}</p>
										</div>
									</div>
								</div>
								<div class="col-12 mt-1 mt-lg-4">
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
														form.appraiser_sale ? form.appraiser_sale.name : ""
													}}
												</p>
											</div>
											<div
												v-if="editAppraiser && edit"
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
													form.appraiser_business_manager
														? form.appraiser_business_manager.name
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
													form.appraiser_perform
														? form.appraiser_perform.name
														: ""
												}}
											</p>
										</div>
										<div class="d-flex container_content">
											<strong class="margin_content_inline"
												>Kiểm soát viên:</strong
											>
											<p>
												{{
													form.appraiser_control
														? form.appraiser_control.name
														: ""
												}}
											</p>
										</div>
										<div class="d-flex container_content">
											<strong class="margin_content_inline"
												>Thẩm định viên:</strong
											>
											<p>{{ form.appraiser ? form.appraiser.name : "" }}</p>
										</div>

										<div class="d-flex container_content">
											<strong class="margin_content_inline"
												>Hành chính viên:</strong
											>
											<p>
												{{
													form.administrative ? form.administrative.name : ""
												}}
											</p>
										</div>

										<div class="d-flex container_content">
											<strong class="margin_content_inline"
												>Đại diện theo pháp luật:</strong
											>
											<p>
												{{
													form.appraiser_manager
														? form.appraiser_manager.name
														: ""
												}}
											</p>
										</div>
										<div class="d-flex container_content">
											<strong class="margin_content_inline"
												>Đại diện ủy quyền:</strong
											>
											<p>
												{{
													form.appraiser_confirm
														? form.appraiser_confirm.name
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
		<PaymentCertificateHistories
			v-if="form"
			:key="keyRender"
			@getDetail="getDetail"
			:form="form"
			:editPayments="editPayment"
			:user="user"
			:toast="$toast"
		/>
		<div class="btn-history">
			<button class="btn btn-orange btn-history" @click="showDrawer">
				<img src="@/assets/icons/ic_log_history.svg" alt="history" />
			</button>
		</div>
		<a-drawer
			v-if="!isMobile()"
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
			v-if="form.status !== 1 || form.general_asset.length > 0 ? true : false"
			class="col-12"
			:style="isMobile() ? { padding: '0' } : {}"
		>
			<div class="card">
				<div class="card-title">
					<div class="d-flex justify-content-between align-items-center">
						<h3 class="title">Kết quả thẩm định</h3>
						<div
							v-if="
								form.general_asset.length > 0 &&
									form.appraiser_perform &&
									editItemList &&
									(edit || add) &&
									user_id === form.appraiser_perform.user_id
							"
							@click="handleShowAppraiseList"
							class="btn-edit"
						>
							<img src="@/assets/icons/ic_edit_3.svg" alt="add" />
						</div>
					</div>
				</div>
				<div class="card-body card-info">
					<div class="row">
						<div
							v-if="
								form.general_asset.length === 0 &&
									form.appraiser_perform &&
									form.status === 2 &&
									(edit || add) &&
									user_id === form.appraiser_perform.user_id
							"
							class="col-12 d-flex mt-2 justify-content-center"
						>
							<button
								class="btn btn_list_appraise-orange text-nowrap mr-3"
								@click.prevent="handleShowAppraiseList"
							>
								Chọn tài sản thẩm định
							</button>
							<button
								v-if="this.isCheckRealEstate !== true"
								class="btn btn_list_appraise-orange text-nowrap"
								@click.prevent="handleImportAppraise"
							>
								Import tài sản thẩm định
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
								Hồ sơ chưa khai báo thông tin tài sản thẩm định
							</div>
						</div>
						<div
							v-if="form.general_asset.length > 0"
							class="col-12 mt-2 table-wrapper"
						>
							<a-table
								bordered
								:columns="columnAssets"
								:data-source="form.general_asset"
								table-layout="top"
								class="table_appraise_list"
								:rowKey="record => record.id"
							>
								<template slot="asset" slot-scope="asset">
									<p :id="asset.id" class="text-none mb-0">{{ asset.name }}</p>
									<!-- <b-tooltip :target="(asset.id).toString()">{{asset.name}}</b-tooltip> -->
								</template>
								<template slot="land" slot-scope="land">
									<p class="text-none mb-0">
										{{
											land.properties &&
											land.properties.length > 0 &&
											land.properties[0].property_detail &&
											land.properties[0].property_detail.length > 0
												? land.properties[0].property_detail[0]
														.land_type_purpose.acronym || ""
												: ""
										}}
									</p>
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
								<template slot="data" slot-scope="data">
									<button
										class="link-detail text-none mb-0"
										@click.prevent="handleDetail(data)"
									>
										{{
											`${showAcronym(data.asset_type.dictionary_acronym)}_` +
												data.general_asset_id
										}}
									</button>
								</template>
							</a-table>
						</div>
						<div
							v-if="form.general_asset.length > 0"
							class="d-flex col-12 justify-content-end mt-3"
						>
							<span class="total mt-1">Tổng cộng</span>
							<div class="d-flex container_total justify-content-between">
								<div>
									{{
										total_price_appraise
											? formatNumber(total_price_appraise)
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
		<div
			v-if="form.general_asset.length > 0 && printConfig"
			class="col-12"
			:style="isMobile() ? { padding: '0' } : {}"
		>
			<div
				class="d-flex flex-column flex-lg-row justify-content-around align-items-center"
			>
				<div class="card w-100 mr-lg-2">
					<div class="card-title text-center">
						<h3 class="title title_input_content">Tài liệu tự động</h3>
					</div>
					<div class="card-body card-info">
						<div class="column">
							<div style="padding: 2px 15px" class="w-100">
								<div class="row input_download_certificate">
									<div
										class="d-flex align-items-center col"
										:class="{ img_filter: !isViewAutomationDocument }"
									>
										<img
											class="img_input_download"
											src="@/assets/icons/ic_document.svg"
											alt="document"
											:class="{ cursor_pointer: isViewAutomationDocument }"
											@click="
												isViewAutomationDocument && downloadCertificate(idData)
											"
										/>
										<div
											class="title_input_content title_input_download"
											:class="{ cursor_pointer: isViewAutomationDocument }"
											@click="
												isViewAutomationDocument && downloadCertificate(idData)
											"
										>
											{{ filterDocumentName[0] || "Chứng thư thẩm định" }}
										</div>
									</div>
									<div
										v-if="isViewAutomationDocument"
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<div @click="viewCertificate(idData)">
											<img
												src="@/assets/icons/ic_search_3.svg"
												alt="search"
												class="img_document_action"
											/>
										</div>
										<!-- <div style="margin-left:10px" @click="downloadCertificate(idData)">
											<img src="@/assets/icons/ic_download_2.svg" alt="download_2" class="img_document_action">
										</div> -->
									</div>
								</div>
							</div>
							<div style="padding: 2px 15px" class="w-100">
								<div class="row input_download_certificate">
									<div
										class="d-flex align-items-center col"
										:class="{ img_filter: !isViewAutomationDocument }"
									>
										<img
											class="img_input_download"
											src="@/assets/icons/ic_document.svg"
											alt="document"
											:class="{ cursor_pointer: isViewAutomationDocument }"
											@click="
												isViewAutomationDocument &&
													downloadReportCertificate(idData)
											"
										/>
										<div
											class="title_input_content title_input_download"
											:class="{ cursor_pointer: isViewAutomationDocument }"
											@click="
												isViewAutomationDocument &&
													downloadReportCertificate(idData)
											"
										>
											{{ filterDocumentName[1] || "Báo cáo thẩm định" }}
										</div>
									</div>
									<div
										v-if="isViewAutomationDocument"
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<div @click="viewReportCertificate(idData)">
											<img
												src="@/assets/icons/ic_search_3.svg"
												alt="search"
												class="img_document_action"
											/>
										</div>
										<!-- <div style="margin-left:10px" @click="downloadReportCertificate(idData)">
											<img src="@/assets/icons/ic_download_2.svg" alt="download_2" class="img_document_action">
										</div> -->
									</div>
								</div>
							</div>
							<div style="padding: 2px 15px" class="w-100">
								<div class="row input_download_certificate">
									<div
										class="d-flex align-items-center col"
										:class="{ img_filter: !isViewAutomationDocument }"
									>
										<img
											class="img_input_download"
											src="@/assets/icons/ic_document.svg"
											alt="document"
											:class="{ cursor_pointer: isViewAutomationDocument }"
											@click="
												isViewAutomationDocument && downloadAppendix1(idData)
											"
										/>
										<div
											class="title_input_content title_input_download"
											:class="{ cursor_pointer: isViewAutomationDocument }"
											@click="
												isViewAutomationDocument && downloadAppendix1(idData)
											"
										>
											{{ filterDocumentName[2] || "Bảng điều chỉnh QSDĐ" }}
										</div>
									</div>
									<div
										v-if="isViewAutomationDocument"
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<div @click="viewAppendix1(idData)">
											<img
												src="@/assets/icons/ic_search_3.svg"
												alt="search"
												class="img_document_action"
											/>
										</div>
										<!-- <div style="margin-left:10px" @click="downloadAppendix1(idData)">
											<img src="@/assets/icons/ic_download_2.svg" alt="download_2" class="img_document_action">
										</div> -->
									</div>
								</div>
							</div>
							<div
								style="padding: 2px 15px"
								class="w-100"
								v-if="
									(!isApartment && isCheckConstruction) || !isCheckRealEstate
								"
							>
								<div class="row input_download_certificate">
									<div
										class="d-flex align-items-center col"
										:class="{ img_filter: !isViewAutomationDocument }"
									>
										<img
											class="img_input_download"
											src="@/assets/icons/ic_document.svg"
											alt="document"
											:class="{ cursor_pointer: isViewAutomationDocument }"
											@click="
												isViewAutomationDocument && downloadAppendix2(idData)
											"
										/>
										<div
											class="title_input_content title_input_download"
											:class="{ cursor_pointer: isViewAutomationDocument }"
											@click="
												isViewAutomationDocument && downloadAppendix2(idData)
											"
										>
											{{ filterDocumentName[3] || "Bảng điều chỉnh CTXD" }}
										</div>
									</div>
									<div
										v-if="isViewAutomationDocument"
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<div @click="viewAppendix2(idData)">
											<img
												src="@/assets/icons/ic_search_3.svg"
												alt="search"
												class="img_document_action"
											/>
										</div>
										<!-- <div style="margin-left:10px" @click="downloadAppendix2(idData)">
											<img src="@/assets/icons/ic_download_2.svg" alt="download_2" class="img_document_action">
										</div> -->
									</div>
								</div>
							</div>
							<div style="padding: 2px 15px" class="w-100">
								<div class="row input_download_certificate">
									<div
										class="d-flex align-items-center col"
										:class="{ img_filter: !isViewAutomationDocument }"
									>
										<img
											class="img_input_download"
											src="@/assets/icons/ic_document.svg"
											alt="document"
											:class="{ cursor_pointer: isViewAutomationDocument }"
											@click="
												isViewAutomationDocument && downloadAppendix3(idData)
											"
										/>
										<div
											class="title_input_content title_input_download"
											:class="{ cursor_pointer: isViewAutomationDocument }"
											@click="
												isViewAutomationDocument && downloadAppendix3(idData)
											"
										>
											{{ filterDocumentName[4] || "Hình ảnh hiện trạng" }}
										</div>
									</div>
									<div
										v-if="isViewAutomationDocument"
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<div @click="viewAppendix3(idData)">
											<img
												src="@/assets/icons/ic_search_3.svg"
												alt="search"
												class="img_document_action"
											/>
										</div>
										<!-- <div style="margin-left:10px" @click="downloadAppendix3(idData)">
											<img src="@/assets/icons/ic_download_2.svg" alt="download_2" class="img_document_action">
										</div> -->
									</div>
								</div>
							</div>
							<div
								style="padding: 2px 15px"
								class="w-100"
								v-if="isCheckRealEstate"
							>
								<div class="row input_download_certificate">
									<div
										class="d-flex align-items-center col"
										:class="{ img_filter: !isViewAutomationDocument }"
									>
										<img
											class="img_input_download"
											src="@/assets/icons/ic_document.svg"
											alt="document"
											:class="{ cursor_pointer: isViewAutomationDocument }"
											@click="
												isViewAutomationDocument &&
													downloadAssetDocument(idData)
											"
										/>
										<div
											class="title_input_content title_input_download"
											:class="{ cursor_pointer: isViewAutomationDocument }"
											@click="
												isViewAutomationDocument &&
													downloadAssetDocument(idData)
											"
										>
											{{ filterDocumentName[5] || "Phiếu thu thập TSSS" }}
										</div>
									</div>
									<div
										v-if="isViewAutomationDocument"
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<div @click="viewAssetDocument(idData)">
											<img
												src="@/assets/icons/ic_search_3.svg"
												alt="search"
												class="img_document_action"
											/>
										</div>
										<!-- <div style="margin-left:10px" @click="downloadAssetDocument(idData)">
											<img src="@/assets/icons/ic_download_2.svg" alt="download_2" class="img_document_action">
										</div> -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card w-100">
					<div class="card-title text-center">
						<h3 class="title title_input_content">Tài liệu chính thức</h3>
					</div>
					<div class="card-body card-info">
						<div class="column">
							<div style="padding: 2px 15px" class="w-100">
								<div class="row input_download_certificate">
									<div class="d-flex align-items-center col">
										<img
											class="img_input_download"
											src="@/assets/icons/ic_document.svg"
											alt="document"
											:class="{ img_filter: !isCertificateReport }"
										/>
										<div
											class="title_input_content title_input_download cursor_pointer"
											v-if="isCertificateReport"
											@click="downloadDocumentFile('certificate_report')"
										>
											{{ certificatReportName }}
										</div>

										<div class="title_input_content title_color" v-else>
											{{ filterDocumentName[0] }}
										</div>
									</div>
									<div
										v-if="
											isCertificateReport &&
												(form.status === 1 ||
													form.status === 2 ||
													form.status === 3)
										"
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<font-awesome-icon
											@click="deletedDocumentFile('certificate_report')"
											:style="{
												color: 'lightgray',
												position: 'absolute',
												height: '1.5rem',
												width: '1.5rem'
											}"
											class="cursor_pointer"
											icon="trash-alt"
											size="1x"
										/>
									</div>
									<div
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<div class="d-flex align-items-center">
											<font-awesome-icon
												:style="{
													color: '#2682bfad',
													position: 'absolute',
													height: '2rem',
													width: '2rem'
												}"
												icon="cloud-upload-alt"
												size="2x"
											/>
											<input
												class="btn-upload-mini"
												@click="checkFileUpload('certificate_report')"
											/>
											<input
												type="file"
												ref="certificate_report"
												id="upload_certificate_report"
												accept=".doc, .docx, application/pdf"
												@change="onUploadDocument('certificate_report', $event)"
												hidden
											/>
										</div>
									</div>
								</div>
							</div>
							<div style="padding: 2px 15px" class="w-100">
								<div class="row input_download_certificate">
									<div class="d-flex align-items-center col">
										<img
											class="img_input_download"
											src="@/assets/icons/ic_document.svg"
											alt="document"
											:class="{ img_filter: !isAppraisalReport }"
										/>
										<div
											class="title_input_content title_input_download cursor_pointer"
											v-if="isAppraisalReport"
											@click="downloadDocumentFile('appraisal_report')"
										>
											{{ appraisalReportName }}
										</div>
										<div class="title_input_content title_color" v-else>
											{{ filterDocumentName[1] || "Báo cáo thẩm định" }}
										</div>
									</div>
									<div
										v-if="
											isAppraisalReport &&
												(form.status === 1 ||
													form.status === 2 ||
													form.status === 3)
										"
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<font-awesome-icon
											@click="deletedDocumentFile('appraisal_report')"
											:style="{
												color: 'lightgray',
												position: 'absolute',
												height: '1.5rem',
												width: '1.5rem'
											}"
											class="cursor_pointer"
											icon="trash-alt"
											size="1x"
										/>
									</div>
									<div
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<div class="d-flex align-items-center">
											<font-awesome-icon
												:style="{
													color: '#2682bfad',
													position: 'absolute',
													height: '2rem',
													width: '2rem'
												}"
												icon="cloud-upload-alt"
												size="2x"
											/>
											<input
												class="btn-upload-mini"
												@click="checkFileUpload('appraisal_report')"
											/>
											<input
												type="file"
												ref="appraisal_report"
												id="upload_appraisal_report"
												accept=".doc, .docx, application/pdf"
												@change="onUploadDocument('appraisal_report', $event)"
												hidden
											/>
										</div>
									</div>
								</div>
							</div>
							<div style="padding: 2px 15px" class="w-100">
								<div class="row input_download_certificate">
									<div class="d-flex align-items-center col">
										<img
											class="img_input_download"
											src="@/assets/icons/ic_document.svg"
											alt="document"
											:class="{ img_filter: !isAppendix1Report }"
										/>
										<div
											class="title_input_content title_input_download cursor_pointer"
											v-if="isAppendix1Report"
											@click="downloadDocumentFile('appendix1_report')"
										>
											{{ appendix1ReportName }}
										</div>
										<div class="title_input_content title_color" v-else>
											{{ filterDocumentName[2] || "Bảng điều chỉnh QSDĐ" }}
										</div>
									</div>
									<div
										v-if="
											isAppendix1Report &&
												(form.status === 1 ||
													form.status === 2 ||
													form.status === 3)
										"
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<font-awesome-icon
											@click="deletedDocumentFile('appendix1_report')"
											:style="{
												color: 'lightgray',
												position: 'absolute',
												height: '1.5rem',
												width: '1.5rem'
											}"
											class="cursor_pointer"
											icon="trash-alt"
											size="1x"
										/>
									</div>
									<div
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<div class="d-flex align-items-center">
											<font-awesome-icon
												:style="{
													color: '#2682bfad',
													position: 'absolute',
													height: '2rem',
													width: '2rem'
												}"
												icon="cloud-upload-alt"
												size="2x"
											/>
											<input
												class="btn-upload-mini"
												@click="checkFileUpload('appendix1_report')"
											/>
											<input
												type="file"
												ref="appendix1_report"
												id="upload_appendix1_report"
												accept=".doc, .docx, application/pdf"
												@change="onUploadDocument('appendix1_report', $event)"
												hidden
											/>
										</div>
									</div>
								</div>
							</div>
							<div
								style="padding: 2px 15px"
								class="w-100"
								v-if="
									(!isApartment && isCheckConstruction) || !isCheckRealEstate
								"
							>
								<div class="row input_download_certificate">
									<div class="d-flex align-items-center col">
										<img
											class="img_input_download"
											src="@/assets/icons/ic_document.svg"
											alt="document"
											:class="{ img_filter: !isAppendix2Report }"
										/>
										<div
											class="title_input_content title_input_download cursor_pointer"
											v-if="isAppendix2Report"
											@click="downloadDocumentFile('appendix2_report')"
										>
											{{ appendix2ReportName }}
										</div>
										<div class="title_input_content title_color" v-else>
											{{ filterDocumentName[3] || "Bảng điều chỉnh CTXD" }}
										</div>
									</div>
									<div
										v-if="
											isAppendix2Report &&
												(form.status === 1 ||
													form.status === 2 ||
													form.status === 3)
										"
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<font-awesome-icon
											@click="deletedDocumentFile('appendix2_report')"
											:style="{
												color: 'lightgray',
												position: 'absolute',
												height: '1.5rem',
												width: '1.5rem'
											}"
											class="cursor_pointer"
											icon="trash-alt"
											size="1x"
										/>
									</div>
									<div
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<div class="d-flex align-items-center">
											<font-awesome-icon
												:style="{
													color: '#2682bfad',
													position: 'absolute',
													height: '2rem',
													width: '2rem'
												}"
												icon="cloud-upload-alt"
												size="2x"
											/>
											<input
												class="btn-upload-mini"
												@click="checkFileUpload('appendix2_report')"
											/>
											<input
												type="file"
												ref="appendix2_report"
												id="upload_appendix2_report"
												accept=".doc, .docx, application/pdf"
												@change="onUploadDocument('appendix2_report', $event)"
												hidden
											/>
										</div>
									</div>
								</div>
							</div>
							<div style="padding: 2px 15px" class="w-100">
								<div class="row input_download_certificate">
									<div class="d-flex align-items-center col">
										<img
											class="img_input_download"
											src="@/assets/icons/ic_document.svg"
											alt="document"
											:class="{ img_filter: !isAppendix3Report }"
										/>
										<div
											class="title_input_content title_input_download cursor_pointer"
											v-if="isAppendix3Report"
											@click="downloadDocumentFile('appendix3_report')"
										>
											{{ appendix3ReportName }}
										</div>
										<div class="title_input_content title_color" v-else>
											{{ filterDocumentName[4] || "Hình ảnh hiện trạng" }}
										</div>
									</div>
									<div
										v-if="
											isAppendix3Report &&
												(form.status === 1 ||
													form.status === 2 ||
													form.status === 3)
										"
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<font-awesome-icon
											@click="deletedDocumentFile('appendix3_report')"
											:style="{
												color: 'lightgray',
												position: 'absolute',
												height: '1.5rem',
												width: '1.5rem'
											}"
											class="cursor_pointer"
											icon="trash-alt"
											size="1x"
										/>
									</div>
									<div
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<div class="d-flex align-items-center">
											<font-awesome-icon
												:style="{
													color: '#2682bfad',
													position: 'absolute',
													height: '2rem',
													width: '2rem'
												}"
												icon="cloud-upload-alt"
												size="2x"
											/>
											<input
												class="btn-upload-mini"
												@click="checkFileUpload('appendix3_report')"
											/>
											<input
												type="file"
												ref="appendix3_report"
												id="upload_appendix3_report"
												accept=".doc, .docx, application/pdf"
												@change="onUploadDocument('appendix3_report', $event)"
												hidden
											/>
										</div>
									</div>
								</div>
							</div>
							<div
								style="padding: 2px 15px"
								class="w-100"
								v-if="isCheckRealEstate"
							>
								<div class="row input_download_certificate">
									<div class="d-flex align-items-center col">
										<img
											class="img_input_download"
											src="@/assets/icons/ic_document.svg"
											alt="document"
											:class="{ img_filter: !isComparisionAssetReport }"
										/>
										<div
											class="title_input_content title_input_download cursor_pointer"
											v-if="isComparisionAssetReport"
											@click="downloadDocumentFile('comparision_asset_report')"
										>
											{{ comparisionAssetReportName }}
										</div>
										<div class="title_input_content title_color" v-else>
											{{ filterDocumentName[5] || "Phiếu thu thập TSSS" }}
										</div>
									</div>
									<div
										v-if="
											isComparisionAssetReport &&
												(form.status === 1 ||
													form.status === 2 ||
													form.status === 3)
										"
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<font-awesome-icon
											@click="deletedDocumentFile('comparision_asset_report')"
											:style="{
												color: 'lightgray',
												position: 'absolute',
												height: '1.5rem',
												width: '1.5rem'
											}"
											class="cursor_pointer"
											icon="trash-alt"
											size="1x"
										/>
									</div>
									<div
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									>
										<div class="d-flex align-items-center">
											<font-awesome-icon
												:style="{
													color: '#2682bfad',
													position: 'absolute',
													height: '2rem',
													width: '2rem'
												}"
												icon="cloud-upload-alt"
												size="2x"
											/>
											<input
												class="btn-upload-mini"
												@click="checkFileUpload('comparision_asset_report')"
											/>
											<input
												type="file"
												ref="comparision_asset_report"
												id="upload_comparision_asset_report"
												accept=".doc, .docx, application/pdf"
												@change="
													onUploadDocument('comparision_asset_report', $event)
												"
												hidden
											/>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div
			v-if="
				form.general_asset.length > 0 || form.status === 1 || form.status === 2
			"
			class="col-12"
			:style="isMobile() ? { padding: '0' } : {}"
		>
			<div class="card" :style="isMobile() ? { 'margin-bottom': '150px' } : {}">
				<div class="card-title">
					<div class="d-flex justify-content-between align-items-center">
						<h3 class="title">Tài liệu đính kèm</h3>
					</div>
				</div>
				<div class="card-body card-info">
					<div class="row">
						<div class="col-12 mt-3">
							<div
								class="input_upload_file d-flex justify-content-center align-items-center"
							>
								<font-awesome-icon
									:style="{ color: 'orange', position: 'absolute' }"
									icon="cloud-upload-alt"
									size="5x"
								/>
								<input
									class="btn-upload"
									type="file"
									ref="file"
									id="image_property"
									multiple
									accept="image/png, image/gif, image/jpeg, image/jpg, .doc, .docx, .xlsx, .xls, application/pdf"
									@change="onImageChange($event)"
								/>
							</div>
						</div>
					</div>
					<div class="row mt-3">
						<div
							v-for="(file, index) in form.other_documents.filter(
								i => i.description === 'appendix' || i.description === 'other'
							)"
							:key="index"
							class="d-flex"
						>
							<div
								style="cursor: pointer"
								@click="downloadOtherFile(file)"
								class="d-flex"
							>
								<img
									class="mr-1"
									style="width: 1rem"
									src="@/assets/icons/ic_taglink.svg"
									alt="tag_2"
								/>
								<div class="mr-3">{{ file.name }}</div>
							</div>
							<!-- <img style="cursor: pointer" class="mr-1" @click="downloadOtherFile(file)" src="@/assets/icons/ic_taglink.svg" alt="tag_2"/>
							<div class="mr-3">{{file.name}}</div> -->
							<img
								v-if="
									deleted &&
										(form.status === 1 ||
											form.status === 2 ||
											form.status === 3)
								"
								style="cursor: pointer; width: 1rem"
								@click="deleteOtherFile(file, index)"
								src="@/assets/icons/ic_delete_2.svg"
								alt="tag_2"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>
		<Footer
			v-if="jsonConfig"
			:style="isMobile() ? { bottom: '60px' } : {}"
			:key="form.status + '_' + form.sub_status"
			:form="form"
			:jsonConfig="jsonConfig"
			:status="form.status"
			:sub_status="form.sub_status"
			:profile="profile"
			:idData="idData"
			:checkVersion="checkVersion"
			@handleFooterAccept="handleFooterAccept"
			@handleEdit="handleEdit"
			@handleCancelCertificate="handleCancelCertificate"
			@onCancel="onCancel"
			@viewDetailAppraise="viewDetailAppraise"
			@viewAppraiseListVersion="viewAppraiseListVersion"
		/>
		<!--
		<div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
			<button v-if="form.status === 3 && form.appraises.length > 0 ? true : false " class="btn btn-white" @click.prevent="viewDetailAppraise">
				<img class="img" src="@/assets/icons/ic_done-orange.svg" alt="edit">
				Cross check
			</button>
			<button v-if="form.status === 3 && form.appraiser && accept && (appraiser_number && user_id === form.appraiser.user_id ) ? true : false "  class="btn btn-white btn-orange" @click.prevent="handleAccept">
					<img class="img" src="@/assets/icons/ic_done.svg" alt="edit">
					Đồng ý phê duyệt
			</button>

			<button v-if="form.status === 3 && form.appraiser && accept && (appraiser_number && user_id === form.appraiser.user_id) ? true : false " class="btn btn-white" @click.prevent="handleDenined">
					<img class="img" src="@/assets/icons/ic_cancel-1.svg" alt="edit">
					Từ chối phê duyệt
			</button>

			<button v-if="form.status === 2 && form.appraiser_perform && (user_id === form.appraiser_perform.user_id) ? true : false " class="btn btn-white btn-orange" @click.prevent="handleSendRequire">
					<img class="img" src="@/assets/icons/ic_done.svg" alt="edit">
					Gửi phê duyệt
			</button>

			<button v-if="form.status === 1 && form.appraiser_sale && (user_id === form.appraiser_sale.user_id || checkRole) ? true : false " class="btn btn-white btn-orange" @click.prevent="handleSendAppraiser">
					<img class="img" src="@/assets/icons/ic_done.svg" alt="edit">
					Gửi thẩm định
			</button>

			<button v-if="form.status === 1 && form.appraiser_sale && (user_id === form.appraiser_sale.user_id || checkRole) ? true : false " class="btn btn-white" @click.prevent="handleEdit(idData)">
				<img class="img" src="@/assets/icons/ic_edit.svg" alt="edit">
				Chỉnh sửa
			</button>
			<b-button-group  class="btn_group" v-if="(form.status === 1 || form.status === 2)">
					<button style="margin-right: 2px" class="btn btn-white" @click="onCancel" type="button">
						<img class="img" src="@/assets/icons/ic_cancel.svg" alt="cancel">Trở về
					</button>
					<b-dropdown class="btn_dropdown" right dropup>
						<b-dropdown-item @click.prevent="handleCancelCertificate">
							<div class="div_item_dropdown">
								<img style="height: 20px" class="img" src="@/assets/icons/ic_destroy.svg" alt="edit">
									Hủy hồ sơ
							</div>
						</b-dropdown-item>
					</b-dropdown>
			</b-button-group>
				//// trở về
				<button v-if="(form.status !== 1 && form.status !== 2)" class="btn btn-white" @click="onCancel" type="button">
					<img class="img" src="@/assets/icons/ic_cancel.svg" alt="cancel">Trở về
				</button>
		</div> -->

		<!-- <ModalCustomer v-if="showCustomerDialog"/> -->
		<ModalAppraisal
			:key="key_render_appraisal"
			v-if="showAppraisalDialog"
			:data="form"
			:idData="idData"
			:status="status"
			:requiredAppraiserPerform="null"
			:requiredAppraiser="null"
			:ModalEdit="true"
			@cancel="showAppraisalDialog = false"
			@updateAppraisal="updateAppraisal"
		/>

		<ModalAppraiseInfomation
			v-if="showAppraiseInformationDialog"
			:data="form"
			:idData="idData"
			:editDocument="editDocument"
			:typeAppraiseProperty="typeAppraiseProperty"
			@cancel="showAppraiseInformationDialog = false"
			@updateAppraiseInformation="updateAppraiseInformation"
		/>
		<ModalAppraiseList
			v-if="showAppraiseListDialog"
			:data="form"
			:isCheckPrice="isCheckPrice"
			:idData="idData"
			@updateAppraises="updateAppraises"
			@cancel="showAppraiseListDialog = false"
		/>
		<ModalNotificationWithAssignHSTD
			v-if="openNotification"
			@cancel="handleCancel"
			:notification="
				message == 'Từ chối' || message == 'Duyệt' || message == 'Hủy'
					? `Bạn có muốn '${message}' hồ sơ này?`
					: `Bạn có muốn chuyển hồ sơ này sang trạng thái`
			"
			workflowName="hstdConfig"
			:status_next="targetStatus"
			:status_text="message"
			:dataHSTD="form"
			:appraiser="appraiserChangeStage"
			@action="handleAction"
		/>
		<ModalNotificationCertificate
			v-if="openNotificationDenined"
			@cancel="openNotificationDenined = false"
			v-bind:notification="message"
			@action="handleActionDenined"
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
		<ModalAppraisal
			:key="key_render_appraisal"
			v-if="openSendRequire"
			:data="form"
			:idData="idData"
			:status="3"
			:requiredAppraiserPerform="null"
			:requiredAppraiser="null"
			@cancel="openSendRequire = false"
			@updateAppraisal="updateSendRequired"
		/>
		<ModalAppraisal
			:key="key_render_appraisal"
			v-if="openSendAppraiser"
			:data="form"
			:idData="idData"
			:status="2"
			:requiredAppraiserPerform="null"
			:requiredAppraiser="null"
			@cancel="openSendAppraiser = false"
			@updateAppraisal="updateSendAppraiser"
		/>
		<ModalDetailAppraise
			v-if="showDetailAppraise"
			:data="dataDetailAppraise"
			@cancel="showDetailAppraise = false"
		/>
		<!-- <ModalNotificationCertificate
			v-if="isHandleAction"
			@cancel="isHandleAction = false"
			:notification="`Bạn có muốn '${message}' hồ sơ này?`"
			@action="handleAction2"
		/> -->
		<ModalNotificationWithAssignHSTD
			v-if="isHandleAction"
			@cancel="isHandleAction = false"
			:notification="
				message == 'Từ chối' || message == 'Duyệt' || message == 'Hủy'
					? `Bạn có muốn '${message}' hồ sơ này?`
					: `Bạn có muốn chuyển hồ sơ này sang trạng thái`
			"
			workflowName="hstdConfig"
			:status_next="targetStatus"
			:status_text="message"
			:dataHSTD="form"
			:appraiser="appraiserChangeStage"
			@action="handleAction2"
		/>

		<ModalAppraiseListVersion
			v-if="isShowAppraiseListVersion"
			:data="checkVersion"
			:idData="idData"
			@updateAppraiseVersion="updateAppraiseVersion"
			@cancel="isShowAppraiseListVersion = false"
		/>
		<ModalNotificationCertificate
			v-if="isReUpload"
			@cancel="isReUpload = false"
			v-bind:notification="reUploadMessage"
			@action="openFile"
		/>
		<ModalDelete
			v-if="deleteUploadDocument"
			@cancel="deleteUploadDocument = false"
			@action="deleteDocument"
		/>
	</div>
</template>
<style lang="scss">
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "../../../node_modules/leaflet/dist/leaflet.css";
</style>
<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { useWorkFlowConfig } from "@/store/workFlowConfig";
import PaymentCertificateHistories from "./component/PaymentCertificateHistories";

import ModalDelete from "@/components/Modal/ModalDelete";
import ModalViewDocument from "./component/modals/ModalViewDocument";
import ModalNotificationCertificate from "@/components/Modal/ModalNotificationCertificate";
import ModalNotificationWithAssignHSTD from "@/components/Modal/ModalNotificationWithAssignHSTD";

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
import CertificationBrief from "@/models/CertificationBrief";
import Certificate from "@/models/Certificate";
import WareHouse from "@/models/WareHouse";
import { Timeline, Drawer } from "ant-design-vue";
import moment from "moment";
import ModalCustomer from "./component/modals/ModalCustomer";
import ModalAppraisal from "./component/modals/ModalAppraisal";
import ModalAppraiseInfomation from "./component/modals/ModalAppraiseInfomation";
import ModalAppraiseList from "./component/modals/ModalAppraiseList";
import ModalDetailAppraise from "./component/modals/ModalDetailAppraise";
import File from "@/models/File";
import axios from "@/plugins/axios";
import {
	BTooltip,
	BDropdown,
	BDropdownItem,
	BButtonGroup
} from "bootstrap-vue";
import Footer from "./component/FooterDetail.vue";

import store from "@/store";
import * as types from "@/store/mutation-types";
import ModalAppraiseListVersion from "./component/modals/ModalAppraiseListVersion";
import IconBase from "@/components/IconBase.vue";

Vue.use(Icon);
export default {
	name: "detail_certification_brief",
	components: {
		PaymentCertificateHistories,
		IconBase,
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
		ModalAppraisal,
		ModalAppraiseInfomation,
		ModalAppraiseList,
		ModalDetailAppraise,
		"b-tooltip": BTooltip,
		ModalNotificationCertificate,
		ModalViewDocument,
		ModalDelete,
		"b-dropdown-item": BDropdownItem,
		"b-button-group": BButtonGroup,
		"b-dropdown": BDropdown,
		Footer,
		ModalAppraiseListVersion,
		ModalNotificationWithAssignHSTD
	},
	data() {
		return {
			theme: {
				navItem: "#000000",
				navActiveItem: "#FAA831",
				slider: "#FAA831",
				arrow: "#000000"
			},
			user: {},
			idData: "",
			status: 1,
			sub_status: 1,
			total_price_appraise: "",
			key_render_appraisal: 10000,
			openModalDelete: false,
			showCustomerDialog: false,
			showAppraiseInformationDialog: false,
			showAppraisalDialog: false,
			showAppraiseListDialog: false,
			showCardDetailAppraise: true,
			visible: false,
			showCardDetailTraffic: true,
			showCardDetailEconomicAndSocial: true,
			showCardDetailImage: true,
			openSendRequire: false,
			openSendAppraiser: false,
			customers_step_1: this.customers,
			appraisersManager: [],
			appraisers: [],
			signAppraisers: [],
			form: {
				appraise_date: "",
				appraise_purpose_id: "",
				document_date: "",
				id: "",
				status: 1,
				sub_status: 1,
				base: "",
				num: "",
				date: "",
				certificate_num: "",
				certificate_date: "",
				customer_request: "",
				appraises: [],
				other_documents: [],
				document_type: [],
				note: ""
			},
			file: "",
			documentAppraise: [],
			message: "",
			openNotification: false,
			cancel_certificate: false,
			openNotificationDenined: false,
			filePrint: "",
			isShowPrint: false,
			title: "",
			indexDelete: "",
			id_file_delete: "",
			position_profile: "",
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false,
			exportAction: false,
			checkRole: false,
			showDetailAppraise: false,
			dataDetailAppraise: [],
			appraiser_number: "",
			user_id: "",
			historyList: [],
			isCheckRealEstate: true,
			isCheckConstruction: false,
			isViewAutomationDocument: true,
			targetStatus: "",
			targetSubStatus: "",
			isHandleAction: false,
			editAppraiser: false,
			editItemList: false,
			editInfo: false,
			editDocument: false,
			editPayment: false,
			printConfig: false,
			profile: {},
			config: {},
			checkVersion: true,
			typeAppraiseProperty: [],
			isShowAppraiseListVersion: false,
			isCheckPrice: false,
			isCheckVersion: false,
			isCheckLegal: false,
			changeStatusRequire: {},
			isApartment: false,
			isReUpload: false,
			reUploadMessage: "",
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
	setup() {
		const workFlowConfigStore = useWorkFlowConfig();
		const jsonConfig = ref(null);

		const keyRender = ref(0);
		const appraiserChangeStage = ref(null);
		return {
			appraiserChangeStage,
			jsonConfig,
			workFlowConfigStore,
			keyRender
		};
	},
	beforeRouteEnter: async (to, from, next) => {
		await CertificationBrief.getDetailCertificateBrief(to.query["id"])
			.then(resp => {
				if (resp.data) {
					to.meta["detail"] = resp.data;
					return next();
				} else if (resp.error && resp.error.statusCode) {
					return next("/".resp.error.statusCode);
				}
			})
			.catch(err => {
				return next("/403");
			});
	},
	created() {
		const profile = this.$store.getters.profile;
		if (
			"id" in this.$route.query &&
			this.$route.name === "certification_brief.detail"
		) {
			if (this.$route.meta["detail"]) {
				this.form = Object.assign(this.form, { ...this.$route.meta["detail"] });
				this.idData = this.$route.query.id;
			}
		}
		if (profile.data.user) {
			this.user = profile.data.user;
			this.position_profile =
				profile.data.user.appraiser.appraise_position.acronym;
			this.appraiser_number = profile.data.user.appraiser.appraiser_number;
		}
		this.user_id = profile.data.user.id;
		if (
			this.form.status &&
			(this.form.status == 2 ||
				this.form.status == 3 ||
				this.form.status == 7) &&
			this.position_profile &&
			(this.position_profile === "CHUYEN-VIEN-KINH-DOANH" ||
				this.position_profile === "NHAN-VIEN-KINH-DOANH" ||
				(this.form.appraiser_sale &&
					this.form.appraiser_sale.user_id === this.user_id))
		) {
			this.$toast.open({
				message:
					"Nhân viên kinh doanh không có quyền xem chi tiết hồ sơ này ở bước này, vui lòng liên hệ admin",
				type: "error",
				position: "top-right"
			});
			let url = this.$router.push({
				name: "certification_brief.index"
			});
		}
		this.profile = profile;
		if (profile.data.user.id === this.form.created_by) {
			this.checkRole = true;
		}
		const permission = this.$store.getters.currentPermissions;
		// fix_permission
		permission.forEach(value => {
			if (value === "VIEW_CERTIFICATE_BRIEF") {
				this.view = true;
			}
			if (value === "ADD_CERTIFICATE_BRIEF") {
				this.add = true;
			}
			if (value === "EDIT_CERTIFICATE_BRIEF") {
				this.edit = true;
			}
			if (value === "DELETE_CERTIFICATE_BRIEF") {
				this.deleted = true;
			}
			if (value === "ACCEPT_CERTIFICATE_BRIEF") {
				this.accept = true;
			}
			if (value === "EXPORT_CERTIFICATE_BRIEF") {
				this.exportAction = true;
			}
		});
		this.getDictionary();
	},
	computed: {
		statusDescription() {
			if (this.jsonConfig) {
				const status = this.jsonConfig.principle.find(
					i =>
						i.status === this.form.status &&
						i.sub_status === this.form.sub_status
				);
				return status ? status.description : "";
			}

			return "";
		},

		columnAssets() {
			let dataColumn = [
				{
					title: "Mã TSTĐ",
					align: "left",
					scopedSlots: { customRender: "data" },
					hiddenItem: false
				},
				{
					title: "Version",
					align: "center",
					scopedSlots: { customRender: "version" },
					dataIndex: "version",
					hiddenItem: false
				},
				{
					title: "Loại tài sản",
					align: "left",
					dataIndex: "asset_type.description",
					hiddenItem: false
				},
				{
					title: "Tên tài sản",
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
		},
		filterDocumentName() {
			return this.documentName;
		},
		isCertificateReport() {
			let report = this.getReport("certificate_report");
			if (report) {
				return true;
			}
			return false;
		},
		isAppraisalReport() {
			let report = this.getReport("appraisal_report");
			if (report) {
				return true;
			}
			return false;
		},
		isAppendix1Report() {
			let report = this.getReport("appendix1_report");
			if (report) {
				return true;
			}
			return false;
		},
		isAppendix2Report() {
			let report = this.getReport("appendix2_report");
			if (report) {
				return true;
			}
			return false;
		},
		isAppendix3Report() {
			let report = this.getReport("appendix3_report");
			if (report) {
				return true;
			}
			return false;
		},
		isComparisionAssetReport() {
			let report = this.getReport("comparision_asset_report");
			if (report) {
				return true;
			}
			return false;
		},
		certificatReportName() {
			let report = this.getReport("certificate_report");
			if (report) {
				return report.name;
			}
			return "Chứng thư thẩm định";
		},
		appraisalReportName() {
			let report = this.getReport("appraisal_report");
			if (report) {
				return report.name;
			}
			return "Báo cáo thẩm định";
		},
		appendix1ReportName() {
			let report = this.getReport("appendix1_report");
			if (report) {
				return report.name;
			}
			return "Bảng điều chỉnh QSDĐ";
		},
		appendix2ReportName() {
			let report = this.getReport("appendix2_report");
			if (report) {
				return report.name;
			}
			return "Bảng điều chỉnh CTXD";
		},
		appendix3ReportName() {
			let report = this.getReport("appendix3_report");
			if (report) {
				return report.name;
			}
			return "Hình ảnh hiện trạng";
		},
		comparisionAssetReportName() {
			let report = this.getReport("comparision_asset_report");
			if (report) {
				return report.name;
			}
			return "Phiếu thu thập TSSS";
		},
		getHistoryTextColor() {
			return this.historyList.map(item => {
				return this.loadColor(item);
			});
		}
	},
	methods: {
		async getDetail() {
			await CertificationBrief.getDetailCertificateBrief(this.form.id)
				.then(resp => {
					if (resp.data) {
						this.form = Object.assign(this.form, { ...resp.data });
						// this.keyRender++;
					} else if (resp.error && resp.error.statusCode) {
						this.$toast.open({
							message: resp.error.statusCode,
							type: "error",
							position: "top-right",
							duration: 5000
						});
					}
				})
				.catch(err => {
					this.$toast.open({
						message: err,
						type: "error",
						position: "top-right",
						duration: 5000
					});
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
		getNotificationMessage() {
			switch (
				this.form.status // Sử dụng this.form.status thay vì status
			) {
				case 1:
					return "Bạn có muốn chuyển hồ sơ này sang trạng thái 'Thẩm định' ?";
				case 2:
					return "Bạn có muốn chuyển hồ sơ này sang trạng thái 'Duyệt giá' ?";
				case 3:
					return "Bạn có muốn chuyển hồ sơ này sang trạng thái 'Duyệt phát hành' ?";
				case 7:
					return "Bạn có muốn chuyển hồ sơ này sang trạng thái 'In hồ sơ' ?";
				case 8:
					return "Bạn có muốn chuyển hồ sơ này sang trạng thái 'Bàn giao khách hàng' ?";
				case 9:
					return "Bạn có muốn chuyển hồ sơ này sang trạng thái 'Hoàn thành' ?";
				default:
					return "";
			}
		},
		getReport(type) {
			let report = this.form.other_documents.find(i => i.description === type);
			return report;
		},
		openFile() {
			const id = "upload_" + this.reportType;
			document.getElementById(id).click();
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
		async getDictionary() {
			let resp = this.$store.getters.dictionaries;
			if (resp && resp.length === 0) {
				resp = await WareHouse.getDictionaries();
				store.commit(types.SET_DICTIONARIES, { ...resp });
			}
			this.typeAppraiseProperty = [...resp.data.loai_tai_san];
			this.typeAppraiseProperty.forEach(item => {
				item.description = this.formatSentenceCase(item.description);
			});
		},
		checkNoticeMessage() {
			if (
				this.form.general_asset.length === 0 &&
				this.form.appraiser_perform &&
				this.user_id !== this.form.appraiser_perform.user_id
			) {
				return true;
			} else if (
				this.form.general_asset.length === 0 &&
				this.form.status === 5 &&
				this.form.appraiser_perform &&
				this.user_id === this.form.appraiser_perform.user_id
			) {
				return true;
			} else return false;
		},
		showAcronym(acronym) {
			if (acronym === "KHAC") {
				return "TSK";
			} else if (acronym === "DS") {
				return "DS";
			} else return acronym;
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
			if (data.asset_type.dictionary_acronym === "DS") {
				if (data.asset_type.acronym === "PTVT") {
					routeData = this.$router.resolve({
						name: "certification_asset.vehicle.detail",
						query: {
							id: data.general_asset_id,
							asset_type_id: data.asset_type_id
						}
					});
				} else if (data.asset_type.acronym === "MMTB") {
					routeData = this.$router.resolve({
						name: "certification_asset.machine.detail",
						query: {
							id: data.general_asset_id,
							asset_type_id: data.asset_type_id
						}
					});
				}
			} else if (data.asset_type.dictionary_acronym === "KHAC") {
				routeData = this.$router.resolve({
					name: "certification_asset.other_purpose.detail",
					query: {
						id: data.general_asset_id,
						asset_type_id: data.asset_type_id
					}
				});
			} else if (data.asset_type.acronym === "CC") {
				routeData = this.$router.resolve({
					name: "certification_asset.apartment.detail",
					query: { id: data.asset.asset_id }
				});
			} else {
				routeData = this.$router.resolve({
					name: "certification_asset.detail",
					query: { id: data.asset.asset_id }
				});
			}
			window.open(routeData.href, "_blank");
		},
		async viewDetailAppraise() {
			let ids = [];
			await this.form.real_estate.forEach(item => {
				ids.push(item.real_estate_id);
			});
			const res = await CertificationBrief.getAppraiseCompare(ids);
			if (res.data) {
				if (res.data[0].message) {
					return this.$toast.open({
						message: res.data[0].message,
						type: "error",
						position: "top-right",
						duration: 5000
					});
				}
				this.dataDetailAppraise = res.data;
				this.showDetailAppraise = true;
			} else if (res.error) {
				return this.$toast.open({
					message: res.error.message,
					type: "error",
					position: "top-right",
					duration: 5000
				});
			}
		},
		async getHistoryTimeLine() {
			const res = await CertificationBrief.getHistoryTimeline(this.idData);
			if (res.data) {
				const resp = await WareHouse.getDictionaries();
				if (resp) {
					this.historyList = res.data;
					for (let i = 0; i < this.historyList.length; i++) {
						let e = this.historyList[i];
						if (e.properties.reason_id) {
							let result = resp.data.li_do.filter(
								item => item.id === e.properties.reason_id
							);
							// console.log('répóne',result)
							e.reason_description = result[0].description;
						}
					}
				}

				// console.log('timeline', this.historyList)
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
		formatDateTime(value) {
			return moment(String(value)).format("HH:mm DD/MM/YYYY");
		},
		formatNumber(num) {
			// convert number to dot formatNumber
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
					name: "certification_brief.edit",
					query: {
						id: this.idData
					}
				})
				.catch(_ => {});
		},
		onCancel() {
			return this.$router.push({ name: "certification_brief.index" });
		},
		handleCancelCertificate() {
			this.appraiserChangeStage = null;
			this.openNotification = true;
			this.cancel_certificate = true;
			this.message = "Hủy";
		},
		handleAccept() {
			this.openNotification = true;
			this.cancel_certificate = false;
			this.message = "Duyệt";
		},
		handleDenined() {
			this.openNotificationDenined = true;
			this.cancel_certificate = false;
			this.message = "Từ chối";
		},
		handleSendRequire() {
			if (this.form.general_asset && this.form.general_asset.length > 0) {
				this.openSendRequire = true;
				this.key_render_appraisal += 1;
			} else {
				this.$toast.open({
					message: "Vui lòng chọn tài sản thẩm định",
					type: "error",
					position: "top-right"
				});
			}
		},
		updateSendRequired() {
			this.$router.push({ name: "certification_brief.index" }).catch(_ => {});
		},
		handleSendAppraiser() {
			this.openSendAppraiser = true;
			this.key_render_appraisal += 1;
			this.status = 2;
		},
		updateSendAppraiser() {
			this.$router.push({ name: "certification_brief.index" }).catch(_ => {});
		},
		handleCancel() {
			this.openNotification = false;
			if (this.cancel_certificate) {
				this.cancel_certificate = false;
			}
		},
		handleShowAppraisal() {
			// console.log('-----------',this.form)
			this.key_render_appraisal += 1;
			this.status = this.form.status;
			this.showAppraisalDialog = true;
		},
		handleShowAppraiseInformation() {
			this.showAppraiseInformationDialog = true;
		},
		handleShowAppraiseList() {
			this.showAppraiseListDialog = true;
		},
		handleImportAppraise() {
			return this.$toast.open({
				message: "Hiện tại chức năng này chưa được mở ở phiên bản dùng thử",
				type: "error",
				position: "top-right",
				duration: 3000
			});
		},
		updateAppraiseInformation(dataAppraiseInformation) {
			this.form.appraise_date = dataAppraiseInformation.appraise_date;
			this.form.appraise_purpose_id =
				dataAppraiseInformation.appraise_purpose_id;
			this.form.appraise_purpose = dataAppraiseInformation.appraise_purpose;
			this.form.certificate_date = dataAppraiseInformation.certificate_date;
			this.form.certificate_num = dataAppraiseInformation.certificate_num;
			this.form.document_date = dataAppraiseInformation.document_date;
			this.form.document_num = dataAppraiseInformation.document_num;
			this.form.petitioner_address = dataAppraiseInformation.petitioner_address;
			this.form.petitioner_name = dataAppraiseInformation.petitioner_name;
			this.form.petitioner_phone = dataAppraiseInformation.petitioner_phone;
			this.form.service_fee = dataAppraiseInformation.service_fee;
			this.form.commission_fee = dataAppraiseInformation.commission_fee;
			this.form.petitioner_identity_card =
				dataAppraiseInformation.petitioner_identity_card;
			this.form.document_type = dataAppraiseInformation.document_type;
			this.form.note = dataAppraiseInformation.note;
		},
		updateAppraisal(dataAppraisal) {
			console.log("dataAppraisal", dataAppraisal);
			this.form.appraiser_perform = dataAppraisal.appraiser_perform;
			this.form.appraiser_perform_id = dataAppraisal.appraiser_perform_id;
			this.form.appraiser_confirm_id = dataAppraisal.appraiser_confirm_id;
			(this.form.business_manager_id = dataAppraisal.business_manager_id),
				(this.form.appraiser_business_manager =
					dataAppraisal.appraiser_business_manager),
				(this.form.appraiser_confirm = dataAppraisal.appraiser_confirm);
			this.form.appraiser_manager_id = dataAppraisal.appraiser_manager_id;
			this.form.appraiser_manager = dataAppraisal.appraiser_manager;
			this.form.appraiser_control_id = dataAppraisal.appraiser_control_id;
			this.form.appraiser_control = dataAppraisal.appraiser_control;
			this.form.appraiser = dataAppraisal.appraiser;
			this.form.appraiser_id = dataAppraisal.appraiser_id;
			this.form.administrative = dataAppraisal.administrative;
			this.form.administrative_id = dataAppraisal.administrative_id;
			this.key_render_appraisal += 1;
			this.form.status = this.status;
			this.showAppraisalDialog = false;
		},
		updateAppraises(data) {
			this.form.appraises = data.general_asset;
			this.form.general_asset = data.general_asset;
			this.form.document_type = data.document_type;
			this.form.real_estate = data.real_estate;
			this.getTotalPrice();
		},
		async updateAppraiseVersion(data) {
			this.isShowAppraiseListVersion = false;
			this.checkVersion = [];
			this.form.checkVersion = [];
			this.form.general_asset = data.general_asset;
			this.getTotalPrice();
		},
		async handleAction(note, reason_id) {
			const {
				appraiser_id,
				appraiser_perform_id,
				appraiser_confirm_id,
				appraiser_manager_id,
				appraiser_perform,
				appraiser_confirm,
				appraiser_manager,
				appraiser,
				appraiser_control,
				appraiser_control_id
			} = this.form;
			let dataSend = {
				appraiser_perform,
				appraiser_id,
				appraiser_perform_id,
				appraiser_confirm_id,
				appraiser_confirm,
				appraiser_manager_id,
				appraiser_manager,
				appraiser_control,
				appraiser_control_id,
				appraiser,
				status: 1,
				sub_status: 1,
				status_config: this.jsonConfig.principle,
				status_note: note,
				status_reason_id: reason_id
			};
			if (this.form.status === 2 && !this.cancel_certificate) {
				// change status 2 --> 3
				dataSend.status = 3;
				const res = await CertificationBrief.updateStatusCertificate(
					this.idData,
					dataSend
				);
				if (res.data) {
					this.$toast.open({
						message: "Gửi phê duyệt thành công",
						type: "success",
						position: "top-right",
						duration: 3000
					});
					this.form.status = 3;
				} else if (res.error) {
					this.$toast.open({
						message: res.error.message,
						type: "error",
						position: "top-right",
						duration: 5000
					});
				}
				this.openNotification = false;
			} else if (this.form.status === 3 && !this.cancel_certificate) {
				// change status 3 --> 4
				dataSend.status = 4;
				const res = await CertificationBrief.updateStatusCertificate(
					this.idData,
					dataSend
				);
				if (res.data) {
					this.$toast.open({
						message: "Xác nhận hồ sơ thành công",
						type: "success",
						position: "top-right",
						duration: 3000
					});
					this.form.status = 4;
				} else if (res.error) {
					this.$toast.open({
						message: res.error.message,
						type: "error",
						position: "top-right",
						duration: 5000
					});
				}
				this.openNotification = false;
			} else if (this.cancel_certificate) {
				// change status 2 --> 5
				dataSend.status = 5;
				const res = await CertificationBrief.updateStatusCertificate(
					this.idData,
					dataSend
				);
				if (res.data) {
					this.$toast.open({
						message: "Hủy hồ sơ thành công",
						type: "success",
						position: "top-right",
						duration: 3000
					});
					this.form.status = 5;
				} else if (res.error) {
					this.$toast.open({
						message: res.error.message,
						type: "error",
						position: "top-right",
						duration: 5000
					});
				}
				this.openNotification = false;
				this.cancel_certificate = false;
			}
		},
		checkAppraiser() {
			if (this.form.appraiser_perform_id && this.form.appraiser_id) {
				return true;
			} else {
				return false;
			}
		},
		checkItemList() {
			if (
				this.form.personal_properties.length > 0 ||
				this.form.general_asset.length > 0
			) {
				return true;
			} else {
				return false;
			}
		},
		handleDetailPreCertificate(id) {
			let url = this.$router.resolve({
				name: "pre_certification.detail",
				query: {
					id: id.toString()
				}
			}).href;

			window.open(url, "_blank");
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
				if (require.appraiser) {
					check = this.checkAppraiser();
					if (!check) {
						return "Chưa có thông tin tổ thẩm định";
					}
				}
				if (check && require.appraise_item_list) {
					check = this.checkItemList();
					if (!check) {
						return "Chưa có chi tiết tài sản thẩm định";
					}
				}
			}
			return message;
		},
		handleFooterAccept(target) {
			this.appraiserChangeStage = null;
			let config = this.jsonConfig.principle.find(i => i.id === target.id);
			let message = "";
			if (target.description.toUpperCase() === "HOÀN THÀNH") {
				console.log("Data detail hoàn thành", this.form);
				if (
					this.form.payments &&
					(this.form.payments.length === 0 ||
						(this.form.payments.length === 1 && !this.form.payments[0].id))
				) {
					this.$toast.open({
						message:
							"Vui lòng thanh toán hết dư nợ để chuyển sang trạng thái hoàn thành !",
						type: "error",
						position: "top-right",
						duration: 3000
					});

					return;
				} else if (
					this.form.payments &&
					this.form.payments.length > 0 &&
					this.form.payments[0].id
				) {
					let debt_remain = this.form.service_fee;
					let amount_paid = 0;
					for (let index = 0; index < this.form.payments.length; index++) {
						const element = this.form.payments[index];
						if (element.amount && element.amount > 0) {
							amount_paid += parseFloat(element.amount);
						}
					}
					if (debt_remain - amount_paid > 0) {
						this.$toast.open({
							message:
								"Vui lòng thanh toán hết dư nợ  để chuyển sang trạng thái hoàn thành !",
							type: "error",
							position: "top-right",
							duration: 3000
						});
						return;
					}
				}
			}
			if (config) {
				this.config = config;
				let require = config.require;
				message = this.checkDiffVersion();
				if (message === "" && require) {
					message = this.checkRequired(require, this.data);
				}
				if (message === "") {
					if (config.re_assign)
						this.appraiserChangeStage = {
							id: this.form[config.re_assign],
							type: config.re_assign
						};
					this.targetStatus = config.status;
					this.targetSubStatus = config.sub_status;
					this.message = target.description;
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
		getExpireStatusDate(config) {
			const configTemp = config ? config : this.config;
			let dateConvert = new Date();
			let minutes = configTemp.process_time ? configTemp.process_time : 1440;
			let dateConverted = new Date(dateConvert.getTime() + minutes * 60000);
			let status_expired_at = moment(dateConverted).format("DD-MM-YYYY HH:mm");
			return status_expired_at;
		},
		async handleAction2(note, reason_id, tempAppraiser, estime) {
			const config = this.jsonConfig.principle.find(
				item => item.status === this.targetStatus && item.isActive === 1
			);
			// let status_expired_at_temp = config.process_time
			// 	? await this.getExpireStatusDate(config)
			// 	: null;
			let status_expired_at_temp = estime;
			const {
				appraiser_id,
				appraiser_perform_id,
				appraiser_confirm_id,
				appraiser_manager_id,
				appraiser_perform,
				appraiser_confirm,
				appraiser_manager,
				appraiser,
				appraiser_control,
				appraiser_control_id,
				administrative_id,
				administrative
			} = this.form;
			let dataSend = {
				appraiser_perform,
				appraiser_id,
				appraiser_perform_id,
				appraiser_confirm_id,
				appraiser_confirm,
				appraiser_manager_id,
				appraiser_manager,
				appraiser_control,
				appraiser_control_id,
				appraiser,
				administrative_id,
				administrative,
				status: this.targetStatus,
				sub_status: this.targetSubStatus,
				check_price: this.isCheckPrice,
				check_version: this.isCheckVersion,
				check_legal: this.isCheckLegal,
				required: this.changeStatusRequire,
				status_expired_at: status_expired_at_temp,
				status_note: note,
				status_reason_id: reason_id,
				status_description: this.message,
				status_config: this.jsonConfig.principle
			};

			if (tempAppraiser) {
				dataSend[tempAppraiser.type] = tempAppraiser.id;
			}
			const res = await CertificationBrief.updateStatusCertificate(
				this.idData,
				dataSend
			);
			if (res.data) {
				// this.form.status = this.targetStatus;
				// this.form.sub_status = this.targetSubStatus;
				// this.changeEditStatus();
				await this.getDetail();
				this.$toast.open({
					message: this.message + " thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				if (
					this.form.status &&
					(this.form.status == 2 ||
						this.form.status == 3 ||
						this.form.status == 7) &&
					this.position_profile &&
					(this.position_profile === "CHUYEN-VIEN-KINH-DOANH" ||
						this.position_profile === "NHAN-VIEN-KINH-DOANH" ||
						(this.form.appraiser_sale &&
							this.form.appraiser_sale.user_id === this.user_id))
				) {
					await new Promise(resolve => setTimeout(resolve, 1000));
					this.$toast.open({
						message:
							"Nhân viên kinh doanh không có quyền xem chi tiết hồ sơ này ở bước này, vui lòng liên hệ admin",
						type: "error",
						position: "top-right"
					});
					let url = this.$router.push({
						name: "certification_brief.index"
					});
				}
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
		async handleActionDenined() {
			const {
				appraiser_id,
				appraiser_perform_id,
				appraiser_confirm_id,
				appraiser_manager_id,
				appraiser_perform,
				appraiser_confirm,
				appraiser_manager,
				appraiser,
				appraiser_control,
				appraiser_control_id
			} = this.form;
			let dataSend = {
				appraiser_perform,
				appraiser_id,
				appraiser_perform_id,
				appraiser_confirm_id,
				appraiser_confirm,
				appraiser_manager_id,
				appraiser_manager,
				appraiser_control,
				appraiser_control_id,
				appraiser,
				status: 0
			};
			if (this.form.status === 3) {
				// denined change status 3 ---> 2
				dataSend.status = 2;
				const res = await CertificationBrief.updateStatusCertificate(
					this.idData,
					dataSend
				);
				if (res.data) {
					this.$toast.open({
						message: "Từ chối phê duyệt thành công",
						type: "success",
						position: "top-right",
						duration: 3000
					});
					this.form.status = 2;
				} else if (res.error) {
					this.$toast.open({
						message: res.error.message,
						type: "error",
						position: "top-right",
						duration: 5000
					});
				}
				this.openNotificationDenined = false;
			}
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
					if (this.form.status === 1) {
						res = await File.saleUploadFileCertificate(formData, this.idData);
					} else {
						res = await File.uploadFileCertificate(formData, this.idData);
					}
					if (res.data) {
						// await this.$emit('handleChangeFile', res.data.data)
						this.form.other_documents = res.data.data;
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
		checkFileUpload(type) {
			let message = "";
			let isReUpload = false;
			switch (type) {
				case "certificate_report":
					if (this.isCertificateReport) {
						message = "Chứng thư thẩm định";
						isReUpload = true;
					}
					break;
				case "appraisal_report":
					if (this.isAppraisalReport) {
						message = "Báo cáo thẩm định";
						isReUpload = true;
					}
					break;
				case "appendix1_report":
					if (this.isAppendix1Report) {
						message = "Bảng điều chỉnh QSDĐ";
						isReUpload = true;
					}
					break;
				case "appendix2_report":
					if (this.isAppendix2Report) {
						message = "Bảng điều chỉnh CTXD";
						isReUpload = true;
					}
					break;
				case "appendix3_report":
					if (this.isAppendix3Report) {
						message = "Hình ảnh hiện trạng";
						isReUpload = true;
					}
					break;
				case "comparision_asset_report":
					if (this.isComparisionAssetReport) {
						message = "Phiếu thu thập TSSS";
						isReUpload = true;
					}
					break;
			}
			this.reportType = type;
			if (isReUpload) {
				this.isReUpload = isReUpload;
				this.reUploadMessage = isReUpload
					? message + " đã có, bạn có muốn upload " + message + " mới ?"
					: "";
			} else {
				this.openFile();
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
					res = await File.uploadDocument(formData, this.idData, type);
					if (res.data) {
						this.form.other_documents = res.data.data;
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
			await Certificate.getPrintProof(this.idData).then(resp => {
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
		async exportGYC() {
			await Certificate.getPrintGYC(this.idData).then(resp => {
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
			await Certificate.getPrintHDTDG(this.idData).then(resp => {
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
			await Certificate.getPrintBBTL(this.idData).then(resp => {
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
			await Certificate.getPrintKHTDG(this.idData).then(resp => {
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
		async downloadCertificate() {
			await Certificate.getPrintProof(this.idData).then(resp => {
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
			await Certificate.getPrintReport(this.idData).then(resp => {
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
			await Certificate.getPrintReport(this.idData).then(resp => {
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
			if (this.form.real_estate && this.form.real_estate.length > 0) {
				await this.form.real_estate.forEach(item => {
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
			const res = await WareHouse.getPrint(arrayAsset, this.idData);
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
			// console.log(this.form.real_estate)
			if (this.form.real_estate && this.form.real_estate.length > 0) {
				await this.form.real_estate.forEach(item => {
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
			const res = await WareHouse.getPrint(arrayAsset, this.idData);
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
			await Certificate.getPrint(this.idData).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePrint = file.url;
				}
			});
			this.title = "Tài liệu bảng điều chỉnh QSDĐ";
			this.isShowPrint = true;
		},
		async downloadAppendix1() {
			await Certificate.getPrint(this.idData).then(resp => {
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
			await Certificate.getPrintAppendix(this.idData).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePrint = file.url;
				}
			});
			this.title = "Tài liệu bảng điều chỉnh CTXD";
			this.isShowPrint = true;
		},
		async downloadAppendix2() {
			await Certificate.getPrintAppendix(this.idData).then(resp => {
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
			await Certificate.getPrintImage(this.idData).then(resp => {
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
			await Certificate.getPrintImage(this.idData).then(resp => {
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
			} else {
				this.$toast.open({
					message: `Bạn không có quyền tải tài liệu đính kèm này`,
					type: "success",
					position: "top-right",
					duration: 3000
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
				this.form.other_documents.splice(this.indexDelete, 1);
				// this.files = this.form.files
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
		getTotalPrice() {
			let total_price = 0;
			this.form.general_asset.forEach(item => {
				total_price += +item.total_price;
			});
			this.total_price_appraise = total_price;
		},
		changeEditStatus() {
			let dataJson = this.jsonConfig.principle.filter(
				item =>
					item.status === this.form.status &&
					item.sub_status === this.form.sub_status &&
					item.isActive === 1
			);
			if (dataJson && dataJson.length > 0) {
				this.config = dataJson[0];
				this.editAppraiser = dataJson[0].edit.appraiser
					? dataJson[0].edit.appraiser
					: false;
				this.editItemList = dataJson[0].edit.appraise_item_list
					? dataJson[0].edit.appraise_item_list
					: false;
				this.editInfo = dataJson[0].edit.info ? dataJson[0].edit.info : false;
				this.editDocument = dataJson[0].edit.documentNum
					? dataJson[0].edit.documentNum
					: false;
				this.editPayment = dataJson[0].edit.payments
					? dataJson[0].edit.payments
					: false;
				this.printConfig = dataJson[0].print;
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
			if (
				this.config.check_version &&
				this.config.check_version &&
				this.checkVersion.length > 0
			) {
				message =
					"Sai version. Bạn cần cập nhật lại version trước khi chuyển trạng thái.";
			}
			return message;
		},
		viewAppraiseListVersion() {
			this.isShowAppraiseListVersion = true;
		},
		setDocumentViewStatus() {
			// console.log('this.form.document_type',this.form.document_type)
			let isExportAutomatic = true;
			let isCheckRealEstate = true;
			let isCheckConstruction = false;
			let isApartment = false;
			if (this.form.document_type && this.form.document_type.length > 0) {
				if (
					this.form.document_type.filter(function(item) {
						return item !== "DCN" && item !== "DT" && item !== "CC";
					}).length > 0
				) {
					isCheckRealEstate = false;
					isExportAutomatic = false;
				}
				if (
					this.form.document_type.find(i => i === "CC") &&
					(this.form.document_type.find(i => i === "DCN") ||
						this.form.document_type.find(i => i === "DT"))
				) {
					isExportAutomatic = false;
				}
				if (
					this.form.document_type.length === 1 &&
					this.form.document_type.find(i => i === "CC")
				) {
					isApartment = true;
				}
				if (this.form.document_type.find(i => i === "DCN")) {
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
			let file = this.form.other_documents.find(i => i.description === type);
			if (file) {
				// this.downloadDocument(file)
				this.downloadOtherFile(file);
			} else
				this.openMessage(
					"Không tìm thấy file cần tải. Vui lòng xem refesh lại trang."
				);
		},
		deletedDocumentFile(type) {
			let file = this.form.other_documents.find(i => i.description === type);
			if (file) {
				this.deleteUploadDocument = true;
				this.id_file_delete = file.id;
			} else this.openMessage("Không tìm thấy file cần xóa.");
		},
		async deleteDocument() {
			await Certificate.deleteDocument(this.id_file_delete).then(resp => {
				const file = resp;
				if (file.data) {
					this.form.other_documents = file.data;
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
		isMobile() {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		}
	},
	beforeMount() {
		if (this.form.general_asset && this.form.general_asset.length > 0) {
			this.getTotalPrice();
		}
		this.setDocumentViewStatus();
	},
	async mounted() {
		const config = await this.workFlowConfigStore.getConfigByName(
			"workflowHSTD"
		);
		this.jsonConfig = config.hstdConfig;
		this.changeEditStatus();
		this.checkVersion = this.form.checkVersion;
	},
	watch: {
		"form.document_type": {
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
.form-map {
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
.card-status {
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
.arrowBox {
	margin-top: -10px;
	position: relative;
	background: #007ec6;
	height: 22px;
	line-height: 22px;
	text-align: center;
	color: #fff;
	font-weight: 600;
	font-size: 16px !important;
	display: inline-block;
	cursor: pointer;
	padding: 0 10px 0 0;
	margin-left: 30px;
}
.arrow-right:after {
	content: "";
	position: absolute;
	left: -11px;
	top: 0;
	border-top: 11px solid transparent;
	border-bottom: 11px solid transparent;
	border-right: 11px solid #007ec6;
}

.form-group-container {
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
.detail_certification_brief {
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
.margin_content_inline {
	margin-right: 10px;
}
.container_content {
	min-height: 20px;
	p {
		margin-bottom: unset !important;
	}
}
.btn-export {
	border: 1px solid black;
	color: black;
	margin-top: 8px;
	margin-left: 5px;
	height: 45px;
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
</style>
