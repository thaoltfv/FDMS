<template>
	<div class="d-flex flex-column">	
		<div v-if="!isMobile()" class="d-flex align-items-center ml-3">
						<div class="img-company-desktop mb-3 mt-3">
							<img
								v-if="
									infoCompany.linkImage === '' ||
									infoCompany.linkImage === undefined ||
									infoCompany.linkImage === null
								"
								class="w-100"
								src="@/assets/icons/ic_company.png"
								alt="avatar"
							/>
							<img
								v-if="
									infoCompany.linkImage !== '' &&
									infoCompany.linkImage !== undefined &&
									infoCompany.linkImage !== null
								"
								class="w-100"
								:src="infoCompany.linkImage"
								alt="img"
							/>
						</div>
						<div
							class="row ml-3"
							v-if="
								getNameCompanyDesktop(infoCompany.companyName.toUpperCase())
									.length > 1
							"
						>
							<span
								v-for="(name, index) in getNameCompanyDesktop(
									infoCompany.companyName.toUpperCase()
								)"
								class="col-12 name-company-desktop"
								style="justify-content: left"
							>
								{{ name }}
							</span>
						</div>
						<div class="row ml-3" v-else>
							<span
								class="col-12 name-company-desktop"
								style="justify-content: left"
							>
								{{ getNameCompanyDesktop(infoCompany.companyName.toUpperCase()) }}
							</span>
						</div>
		</div>
		<div v-else class="d-flex flex-column justify-content-center align-items-center">
						<div class="img-company mb-3 mt-3">
							<img
								v-if="
									infoCompany.linkImage === '' ||
									infoCompany.linkImage === undefined ||
									infoCompany.linkImage === null
								"
								class="w-100"
								src="@/assets/icons/ic_company.png"
								alt="avatar"
							/>
							<img
								v-if="
									infoCompany.linkImage !== '' &&
									infoCompany.linkImage !== undefined &&
									infoCompany.linkImage !== null
								"
								class="w-100"
								:src="infoCompany.linkImage"
								alt="img"
							/>
						</div>
						<div class="row">
							<span class="col-2"></span>
							<span class="col-8 name-company">
								{{
									infoCompany.companyName
										? infoCompany.companyName.toUpperCase()
										: ""
								}}</span
							>
							<span class="col-2"></span>
						</div>
		</div>
		<a-tabs default-active-key="chung-thu" size="large" v-model="activeTab" @change="onChangeTab">
			<a-tab-pane key="chung-thu" tab="Tra cứu chứng thư">
				<div v-if="!isMobile()" style="width: 97vw; height: 75vh">
					<!-- <div class="d-flex flex-column align-items-center justify-content-center"> -->
					<!-- <div style="margin-left: 1vw"> -->
					<!-- <div class="d-flex align-items-center ml-3">
						<div class="img-company-desktop mb-3 mt-3">
							<img
								v-if="
									infoCompany.linkImage === '' ||
									infoCompany.linkImage === undefined ||
									infoCompany.linkImage === null
								"
								class="w-100"
								src="@/assets/icons/ic_company.png"
								alt="avatar"
							/>
							<img
								v-if="
									infoCompany.linkImage !== '' &&
									infoCompany.linkImage !== undefined &&
									infoCompany.linkImage !== null
								"
								class="w-100"
								:src="infoCompany.linkImage"
								alt="img"
							/>
						</div>
						<div
							class="row ml-3"
							v-if="
								getNameCompanyDesktop(infoCompany.companyName.toUpperCase())
									.length > 1
							"
						>
							<span
								v-for="(name, index) in getNameCompanyDesktop(
									infoCompany.companyName.toUpperCase()
								)"
								class="col-12 name-company-desktop"
								style="justify-content: left"
							>
								{{ name }}
							</span>
						</div>
						<div class="row ml-3" v-else>
							<span
								class="col-12 name-company-desktop"
								style="justify-content: left"
							>
								{{ getNameCompanyDesktop(infoCompany.companyName.toUpperCase()) }}
							</span>
						</div>
					</div> -->
					<div v-if="!isHaveResult">
						<!-- <div class="space-component"></div>
						<div class="space-component"></div> -->
						<div class="title-component d-flex flex-column">
							<div class="row">
								<span class="col-12 title-name-desktop"> Tra cứu thông tin</span>
							</div>
							<div class="row">
								<span class="col-12 title-name-desktop">
									Chứng thư Thẩm định giá</span
								>
							</div>
						</div>
						<div class="search-component d-flex flex-column mt-4">
							<div class="row">
								<span class="col-4"></span>
								<InputText
									v-model="infoSearch.certificate_id"
									label="ID chứng thư"
									placeholder="Nhập số ID"
									vid="certificate_id"
									class="col-2 label-none"
									rules="required"
									:required="true"
								/>
								<InputText
									v-model="infoSearch.certificate_num"
									label="Số chứng thư"
									placeholder="Nhập số Chứng thư"
									vid="doc_num"
									class="col-2 label-none"
									rules="required"
									:required="true"
								/>
								<span class="col-4"></span>
							</div>
							<div class="row mt-4" style="justify-content: center">
								<div
									class="col-2 d-flex align-items-center"
									style="justify-content: center"
								>
									<button
										class="button-search-desktop col-8"
										@click="lookupCertificate"
										:disabled="!isValidInfoSearch"
									>
										Tra cứu
									</button>
								</div>
							</div>
						</div>
						<div class="other-component">
							<div class="d-flex align-items-center">
								<span class="col-2"></span>
								<hr class="col-3" />
								<span class="col-2 qr-title">hoặc</span>
								<hr class="col-3" />
								<span class="col-2"></span>
							</div>
						</div>

						<div
							class="qr-component d-flex flex-column align-items-center justify-content-center mt-n2"
						>
							<div class="row">
								<!-- <span class="col-2"></span> -->
								<span class="col-12 qr-title"> Quét mã QR trên Chứng thư</span>
								<!-- <span class="col-2"></span> -->
							</div>
							<div
								@click="scanQR"
								style="cursor: pointer"
								class="row d-flex flex-column justify-content-center align-item-center"
							>
								<!-- <div class="box mb-3">
										<img
											class="w-100"
											src="@/assets/images/scan_image.png"
											alt="scan"
										/>
									</div> -->
								<div class="center">
									<img
										class="w-100"
										src="@/assets/images/scan_image.png"
										alt="scan"
									/>
									<div class="square">
										<div class="scan"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div v-else>
						<div class="title-component d-flex flex-column">
							<div class="row">
								<span class="col-12 title-name-desktop"> Thông tin</span>
							</div>
							<div class="row">
								<span class="col-12 title-name-desktop">
									Chứng thư Thẩm định giá</span
								>
							</div>
						</div>
						<div class="row mt-3" style="justify-content: center">
							<div class="col-6">
								<div class="card card-result-desktop">
									<div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Số chứng thư</div>
										<div class="col-6 text-align-right text-custom-color">
											{{ searchResult.certificate_num }}
										</div>
									</div>
									<div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Ngày chứng thư</div>
										<div class="col-6 text-align-right">
											{{ formatDate(searchResult.certificate_date) }}
										</div>
									</div>
									<div
										class="row card-list-desktop"
										style="justify-content: center"
									>
										<div class="col-11 base-line-desktop"></div>
									</div>
									<!-- <div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Tên khách hàng</div>
										<div class="col-6 text-align-right">
											{{ searchResult.petitioner_name }}
										</div>
									</div>
									<div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Số điện thoại khách hàng</div>
										<div class="col-6 text-align-right">
											{{ searchResult.petitioner_phone }}
										</div>
									</div> -->
									<div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Tên tài sản</div>
										<div class="col-6 text-align-right" v-if="!searchResult.other_assets">
											<div
												v-for="asset in searchResult.appraises"
												:key="asset.id"
												class="row"
											>
												<div
													class="col-12 text-align-right"
													style="padding-bottom: 1vh"
												>
													{{ asset.appraise_asset }}
												</div>
											</div>
										</div>
										<div class="col-6 text-align-right" v-else>
											<div
												v-for="asset in searchResult.other_assets"
												:key="asset.name"
												class="row"
											>
												<div
													class="col-12 text-align-right"
													style="padding-bottom: 1vh"
													v-if="asset"
												>
												{{ (asset.asset_type === 'BDS' ? 'Bất động sản' : 'Động sản')+ ' : '+ asset.asset_name }}
											</div>
											</div>
										</div>
									</div>
									<div
										class="row card-list-desktop"
										style="justify-content: center"
									>
										<div class="col-11 base-line-desktop"></div>
									</div>
									<div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Mục đích thẩm định</div>
										<div class="col-6 text-align-right">
											{{ searchResult.appraise_purpose }}
										</div>
									</div>
									<div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Thời điểm thẩm định giá</div>
										<div class="col-6 text-align-right">
											{{ formatDate(searchResult.appraise_date) }}
										</div>
									</div>
									<div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Giá trị thẩm định (VND)</div>
										<div class="col-6 text-align-right text-custom-color">
											{{ !searchResult.other_assets ? formatCurrency(searchResult.asset_price) : formatCurrency(calcTotalOtherAssets(searchResult.other_assets)) }}
										</div>
									</div>
									<div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Thẩm định viên về giá</div>
										<div class="col-6 text-align-right">
											{{ searchResult.appraiser ? searchResult.appraiser.name : ''  }}
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row" style="text-align: center">
							<p
								class="col-12"
								@click="reLookUp"
								style="cursor: pointer; color: #00507c; font-style: italic"
							>
								Tra cứu chứng thư khác
							</p>
						</div>
					</div>
					<div class="footer-component-desktop">
						<div class="d-flex justify-content-center">
							<!-- <span class="col-2"></span> -->
							<hr class="col-12" />
							<!-- <span class="col-2"></span> -->
						</div>
						<div
							class="d-flex justify-content-center align-items-center text-center mt-n4"
						>
							<span class="col-12 ml-n5">
								Powered by
								<img src="@/assets/images/company_logo.png" height="15" />
								Fastvalue
							</span>
						</div>
					</div>
				</div>
				<div v-else style="width: 97vw; height: 80vh">
					<!-- <div class="d-flex flex-column justify-content-center align-items-center">
						<div class="img-company mb-3 mt-3">
							<img
								v-if="
									infoCompany.linkImage === '' ||
									infoCompany.linkImage === undefined ||
									infoCompany.linkImage === null
								"
								class="w-100"
								src="@/assets/icons/ic_company.png"
								alt="avatar"
							/>
							<img
								v-if="
									infoCompany.linkImage !== '' &&
									infoCompany.linkImage !== undefined &&
									infoCompany.linkImage !== null
								"
								class="w-100"
								:src="infoCompany.linkImage"
								alt="img"
							/>
						</div>
						<div class="row">
							<span class="col-2"></span>
							<span class="col-8 name-company">
								{{
									infoCompany.companyName
										? infoCompany.companyName.toUpperCase()
										: ""
								}}</span
							>
							<span class="col-2"></span>
						</div>
					</div> -->
					<div v-if="!isHaveResult">
						<div class="space-component"></div>
						<div class="title-component d-flex flex-column">
							<div class="row">
								<span class="col-2"></span>
								<span class="col-8 title-name"> Tra cứu thông tin</span>
								<span class="col-2"></span>
							</div>
							<div class="row">
								<span class="col-2"></span>
								<span class="col-8 title-name"> Chứng thư Thẩm định giá</span>
								<span class="col-2"></span>
							</div>
						</div>
						<div class="search-component d-flex flex-column mt-4">
							<div class="row">
								<div class="col-2"></div>
								<InputText
									v-model="infoSearch.certificate_id"
									label="ID chứng thư"
									placeholder="Nhập số ID"
									vid="certificate_id"
									class="col-8 label-none"
									rules="required"
									:required="true"
								/>
								<div class="col-2"></div>
							</div>
							<div class="row mt-3">
								<div class="col-2"></div>
								<InputText
									v-model="infoSearch.certificate_num"
									label="Số chứng thư"
									placeholder="Nhập số Chứng thư"
									vid="doc_num"
									class="col-8 label-none"
									rules="required"
									:required="true"
								/>
								<div class="col-2"></div>
							</div>
							<div class="row mt-3">
								<div class="col-2"></div>
								<div class="col-8 d-flex align-items-center">
									<a-button
										class="button-search col-12"
										@click="lookupCertificate"
										:disabled="!isValidInfoSearch"
									>
										Tra cứu
									</a-button>
								</div>
								<div class="col-2"></div>
							</div>
						</div>

						<div class="other-component">
							<div class="d-flex align-items-center">
								<span class="col-2"></span>
								<hr class="col-3" />
								<span class="col-2 other-title">hoặc</span>
								<hr class="col-3" />
								<span class="col-2"></span>
							</div>
						</div>

						<div
							@click="scanQR"
							class="qr-component d-flex flex-column align-items-center justify-content-center mt-n2"
						>
							<div class="row">
								<!-- <span class="col-2"></span> -->
								<span class="col-12 qr-title"> Quét mã QR trên Chứng thư</span>
								<!-- <span class="col-2"></span> -->
							</div>
							<div class="row">
								<!-- <div class="col-2"></div> -->
								<div
									class="d-flex flex-column justify-content-center align-item-center"
								>
									<!-- <div class="box mb-3">
										<img
											class="w-100"
											src="@/assets/images/scan_image.png"
											alt="scan"
										/>
									</div> -->
									<div class="center">
										<img
											class="w-100"
											src="@/assets/images/scan_image.png"
											alt="scan"
										/>
										<div class="square">
											<div class="scan"></div>
										</div>
									</div>
								</div>

								<!-- <div class="col-2"></div> -->
							</div>
						</div>
					</div>
					<div v-else>
						<div class="title-component d-flex flex-column">
							<div class="row">
								<span class="col-12 title-name"> Thông tin</span>
							</div>
							<div class="row">
								<span class="col-12 title-name"> Chứng thư Thẩm định giá</span>
							</div>
						</div>
						<div class="row mt-3" style="justify-content: center">
							<div class="col-11">
								<div class="card card-result-mobile">
									<div class="row card-list-mobile" style="">
										<div class="col-6">
											<div class="row">
												<div class="col-12 text-title">Số chứng thư</div>
												<div class="col-12 text-custom-color text-info-mobile">
													{{ searchResult.certificate_num }}
												</div>
											</div>
										</div>
										<div class="col-6">
											<div class="row">
												<div class="col-12 text-title">Ngày chứng thư</div>
												<div class="col-12 text-info-mobile">
													{{ formatDate(searchResult.certificate_date) }}
												</div>
											</div>
										</div>
									</div>
									<div class="row card-list-mobile" style="justify-content: center">
										<div class="col-11 base-line-mobile"></div>
									</div>
									<!-- <div class="row card-list-mobile" style="">
										<div class="col-12 text-title">Tên khách hàng</div>
										<div class="col-12 text-info-mobile">
											{{ searchResult.petitioner_name }}
										</div>
									</div>
									<div class="row card-list-mobile" style="">
										<div class="col-12 text-title">Số điện thoại khách hàng</div>
										<div class="col-12 text-info-mobile">
											{{ searchResult.petitioner_phone }}
										</div>
									</div> -->
									<div class="row card-list-mobile" style="">
										<div class="col-12 text-title">Tên tài sản</div>
										<div class="col-12 text-info-mobile" v-if="!searchResult.other_assets">
											<div
												v-for="asset in searchResult.appraises"
												:key="asset.id"
												class="row"
											
											>
												<div class="col-12" style="padding-bottom: 1vh">
													{{ asset.appraise_asset }}
												</div>
											</div>
										
										</div>
										<div class="col-12 text-info-mobile" v-else>
											<div
												v-for="asset in searchResult.other_assets"
												:key="asset.id"
												class="row"
											
											>
												<div class="col-12" style="padding-bottom: 1vh" v-if="asset">
													{{ (asset.asset_type === 'BDS' ? 'Bất động sản' : 'Động sản')+ ' : '+ asset.asset_name }}
												</div>
											</div>
										</div>
									</div>
									<div class="row card-list-mobile" style="justify-content: center">
										<div class="col-11 base-line-mobile"></div>
									</div>
									<div class="row card-list-mobile" style="">
										<div class="col-12 text-title">Mục đích thẩm định</div>
										<div class="col-12 text-info-mobile">
											{{ searchResult.appraise_purpose }}
										</div>
									</div>
									<div class="row card-list-mobile" style="">
										<div class="col-12 text-title">Thời điểm thẩm định giá</div>
										<div class="col-12 text-info-mobile">
											{{ formatDate(searchResult.appraise_date) }}
										</div>
									</div>
									<div class="row card-list-mobile" style="">
										<div class="col-12 text-title">Giá trị thẩm định (VND)</div>
										<div class="col-12 text-custom-color text-info-mobile">
											{{ !searchResult.other_assets ? formatCurrency(searchResult.asset_price) : formatCurrency(calcTotalOtherAssets(searchResult.other_assets)) }}
										</div>
									</div>
									<div class="row card-list-mobile" style="">
										<div class="col-12 text-title">Thẩm định viên về giá</div>
										<div class="col-12 text-info-mobile">
											{{ searchResult.appraiser ? searchResult.appraiser.name : ''  }}
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row" style="text-align: center">
							<p
								class="col-12"
								@click="reLookUp"
								style="cursor: pointer; color: #00507c; font-style: italic"
							>
								Tra cứu chứng thư khác
							</p>
						</div>
					</div>

					<div class="footer-component">
						<div class="d-flex justify-content-center">
							<span class="col-2"></span>
							<hr class="col-8" />
							<span class="col-2"></span>
						</div>
						<div class="col-12 justify-content-center mt-n4" style="display: flex">
							<span class="powered-title">
								Powered by
								<img src="@/assets/images/company_logo.png" height="15" />
								Fastvalue
							</span>
						</div>
					</div>
					<div class="popup-camera" v-if="isScanQR">
						<div class="card" style="height: 100vh">
							<div
								class="d-flex flex-column justify-content-center align-items-center"
							>
								<div class="img-company mb-3 mt-3">
									<img
										v-if="
											infoCompany.linkImage === '' ||
											infoCompany.linkImage === undefined ||
											infoCompany.linkImage === null
										"
										class="w-100"
										src="@/assets/icons/ic_company.png"
										alt="avatar"
									/>
									<img
										v-if="
											infoCompany.linkImage !== '' &&
											infoCompany.linkImage !== undefined &&
											infoCompany.linkImage !== null
										"
										class="w-100"
										:src="infoCompany.linkImage"
										alt="img"
									/>
								</div>
								<div class="row">
									<span class="col-2"></span>
									<span class="col-8 name-company">
										{{
											infoCompany.companyName
												? infoCompany.companyName.toUpperCase()
												: ""
										}}</span
									>
									<span class="col-2"></span>
								</div>
							</div>
							<div class="contain-camera mt-3">
								<StreamBarcodeReader
									torch
									no-front-cameras
									@decode="onDecode"
									@loaded="onLoaded"
								></StreamBarcodeReader>
							</div>
						</div>
					</div>
				</div>
				<ModalNotificationQRCode
					v-if="isScanQR"
					v-bind:notification="this.message"
					@cancel="isScanQR = false"
					@action="handleHome"
				/>
			</a-tab-pane>
			<a-tab-pane key="nhan-vien" tab="Tra cứu nhân sự">
				<div v-if="!isMobile()" style="width: 97vw; height: 75vh">
					<!-- <div class="d-flex flex-column align-items-center justify-content-center"> -->
					<!-- <div style="margin-left: 1vw"> -->
					<!-- <div class="d-flex align-items-center ml-3">
						<div class="img-company-desktop mb-3 mt-3">
							<img
								v-if="
									infoCompany.linkImage === '' ||
									infoCompany.linkImage === undefined ||
									infoCompany.linkImage === null
								"
								class="w-100"
								src="@/assets/icons/ic_company.png"
								alt="avatar"
							/>
							<img
								v-if="
									infoCompany.linkImage !== '' &&
									infoCompany.linkImage !== undefined &&
									infoCompany.linkImage !== null
								"
								class="w-100"
								:src="infoCompany.linkImage"
								alt="img"
							/>
						</div>
						<div
							class="row ml-3"
							v-if="
								getNameCompanyDesktop(infoCompany.companyName.toUpperCase())
									.length > 1
							"
						>
							<span
								v-for="(name, index) in getNameCompanyDesktop(
									infoCompany.companyName.toUpperCase()
								)"
								class="col-12 name-company-desktop"
								style="justify-content: left"
							>
								{{ name }}
							</span>
						</div>
						<div class="row ml-3" v-else>
							<span
								class="col-12 name-company-desktop"
								style="justify-content: left"
							>
								{{ getNameCompanyDesktop(infoCompany.companyName.toUpperCase()) }}
							</span>
						</div>
					</div> -->
					<div v-if="!isHaveResultEmployee">
						<!-- <div class="space-component"></div>
						<div class="space-component"></div> -->
						<div class="title-component d-flex flex-column">
							<div class="row">
								<span class="col-12 title-name-desktop"> Tra cứu thông tin nhân sự</span>
							</div>
						</div>
						<div class="search-component d-flex flex-column mt-4">
							<div class="row">
								<span class="col-4"></span>
								<!-- <InputText
									v-model="infoSearchEmployee.name"
									label="Họ tên nhân viên"
									placeholder="Nhập tên nhân viên"
									vid="name"
									class="col-2 label-none"
									rules="required"
									:required="true"
								/> -->
								<InputText
									v-model="infoSearchEmployee.phone"
									label="Số điện thoại"
									placeholder="Nhập số điện thoại nhân viên"
									vid="phone"
									class="col-4 label-none"
									rules="required"
									:required="true"
								/>
								<span class="col-4"></span>
							</div>
							<div class="row mt-4" style="justify-content: center">
								<div
									class="col-2 d-flex align-items-center"
									style="justify-content: center"
								>
									<button
										class="button-search-desktop col-8"
										@click="lookupEmployee"
										:disabled="!isValidInfoSearchEmployee"
									>
										Tra cứu
									</button>
								</div>
							</div>
						</div>
					</div>
					<div v-else>
						<!-- <div class="title-component d-flex flex-column">
							<div class="row">
								<span class="col-12 title-name-desktop">Thông tin nhân viên</span>
							</div>
						</div> -->
						<div class="row justify-content-center">
    <img :src="searchResultEmployee.avatarUrl" alt="Avatar" class="avatar-img" />
  </div>
						<div class="row mt-3" style="justify-content: center">
							<div class="col-6">
								<div class="card card-result-desktop">
									<div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Tên nhân viên</div>
										<div class="col-6 text-align-right text-custom-color">
											{{ searchResultEmployee.name }}
										</div>
									</div>
									<div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Số điện thoại</div>
										<div class="col-6 text-align-right">
											{{ searchResultEmployee.phone }}
										</div>
									</div>
									<div
										class="row card-list-desktop"
										style="justify-content: center"
									>
										<div class="col-11 base-line-desktop"></div>
									</div>
									<div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Chức vụ</div>
										<div class="col-6 text-align-right">
											{{ searchResultEmployee.appraise_position ? searchResultEmployee.appraise_position.description : '' }}
										</div>
									</div>
									<div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Chi nhánh</div>
										<div class="col-6 text-align-right">
											{{ searchResultEmployee.branch ? searchResultEmployee.branch.name : '' }}
										</div>
									</div>
									<div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Số thẻ TĐV về giá (nếu có)</div>
										<div class="col-6 text-align-right">
											{{ searchResultEmployee.appraiser_number ? searchResultEmployee.appraiser_number : '' }}
										</div>
									</div>
									<div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Tình trạng công việc</div>
										<div class="col-6 text-align-right" :class="searchResultEmployee.status_user === 'active' ? 'text-success' : 'text-danger'">
											{{ searchResultEmployee.status_user === 'active' ? 'Đang hoạt động' : 'Đã nghỉ việc' }}
										</div>
									</div>
									<div
										class="row card-list-desktop"
										style="justify-content: space-evenly"
									>
										<div class="col-4 text-title">Ghi chú</div>
										<div class="col-6 text-align-right" v-html="formatNote(searchResultEmployee.note)">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row" style="text-align: center">
							<p
								class="col-12"
								@click="reLookUpEmployee"
								style="cursor: pointer; color: #00507c; font-style: italic"
							>
								Tra cứu nhân viên khác
							</p>
						</div>
					</div>
					<div class="footer-component-desktop">
						<div class="d-flex justify-content-center">
							<!-- <span class="col-2"></span> -->
							<hr class="col-12" />
							<!-- <span class="col-2"></span> -->
						</div>
						<div
							class="d-flex justify-content-center align-items-center text-center mt-n4"
						>
							<span class="col-12 ml-n5">
								Powered by
								<img src="@/assets/images/company_logo.png" height="15" />
								Fastvalue
							</span>
						</div>
					</div>
				</div>
				<div v-else style="width: 97vw; height: 80vh">
					<!-- <div class="d-flex flex-column justify-content-center align-items-center">
						<div class="img-company mb-3 mt-3">
							<img
								v-if="
									infoCompany.linkImage === '' ||
									infoCompany.linkImage === undefined ||
									infoCompany.linkImage === null
								"
								class="w-100"
								src="@/assets/icons/ic_company.png"
								alt="avatar"
							/>
							<img
								v-if="
									infoCompany.linkImage !== '' &&
									infoCompany.linkImage !== undefined &&
									infoCompany.linkImage !== null
								"
								class="w-100"
								:src="infoCompany.linkImage"
								alt="img"
							/>
						</div>
						<div class="row">
							<span class="col-2"></span>
							<span class="col-8 name-company">
								{{
									infoCompany.companyName
										? infoCompany.companyName.toUpperCase()
										: ""
								}}</span
							>
							<span class="col-2"></span>
						</div>
					</div> -->
					<div v-if="!isHaveResultEmployee">
						<div class="space-component"></div>
						<div class="title-component d-flex flex-column">
							<div class="row">
								<span class="col-2"></span>
								<span class="col-8 title-name">Tra cứu thông tin</span>
								<span class="col-2"></span>
							</div>
							<div class="row">
								<span class="col-2"></span>
								<span class="col-8 title-name">Nhân viên</span>
								<span class="col-2"></span>
							</div>
						</div>
						<div class="search-component d-flex flex-column mt-4">
							<!-- <div class="row">
								<div class="col-2"></div>
								<InputText
									v-model="infoSearchEmployee.name"
									label="Họ tên nhân viên"
									placeholder="Nhập họ tên nhân viên cần tìm"
									vid="name"
									class="col-8 label-none"
									rules="required"
									:required="true"
								/>
								<div class="col-2"></div>
							</div> -->
							<div class="row mt-3">
								<div class="col-2"></div>
								<InputText
									v-model="infoSearchEmployee.phone"
									label="Số điện thoại"
									placeholder="Nhập số điện thoại"
									vid="phone"
									class="col-8 label-none"
									rules="required"
									:required="true"
								/>
								<div class="col-2"></div>
							</div>
							<div class="row mt-3">
								<div class="col-2"></div>
								<div class="col-8 d-flex align-items-center">
									<a-button
										class="button-search col-12"
										@click="lookupEmployee"
										:disabled="!isValidInfoSearchEmployee"
									>
										Tra cứu
									</a-button>
								</div>
								<div class="col-2"></div>
							</div>
						</div>
					</div>
					<div v-else>
						<!-- <div class="title-component d-flex flex-column">
							<div class="row">
								<span class="col-12 title-name">Thông tin nhân viên</span>
							</div>
						</div> -->
						<div class="row justify-content-center">
    <img :src="searchResultEmployee.avatarUrl" alt="Avatar" class="avatar-img" />
  </div>
						<div class="row mt-3" style="justify-content: center">
							<div class="col-11">
								<div class="card card-result-mobile">
									<div class="row card-list-mobile" style="">
										<div class="col-6">
											<div class="row">
												<div class="col-12 text-title">Tên nhân viên</div>
												<div class="col-12 text-custom-color text-info-mobile">
													{{ searchResultEmployee.name }}
												</div>
											</div>
										</div>
										<div class="col-6">
											<div class="row">
												<div class="col-12 text-title">Số điện thoại</div>
												<div class="col-12 text-info-mobile">
													{{ searchResultEmployee.phone }}
												</div>
											</div>
										</div>
									</div>
									<div class="row card-list-mobile" style="justify-content: center">
										<div class="col-11 base-line-mobile"></div>
									</div>
									<div class="row card-list-mobile" style="">
										<div class="col-12 text-title">Chức vụ</div>
										<div class="col-12 text-info-mobile">
											{{ searchResultEmployee.appraise_position ? searchResultEmployee.appraise_position.description : '' }}
										</div>
									</div>
									<div class="row card-list-mobile" style="">
										<div class="col-12 text-title">Chi nhánh</div>
										<div class="col-12 text-info-mobile">
											{{ searchResultEmployee.branch ? searchResultEmployee.branch.name : '' }}
										</div>
									</div>
									<div class="row card-list-mobile" style="">
										<div class="col-12 text-title">Số thẻ TĐV về giá (nếu có)</div>
										<div class="col-12 text-info-mobile">
											{{ searchResultEmployee.appraiser_number ? searchResultEmployee.appraiser_number : '' }}
										</div>
									</div>
									<div class="row card-list-mobile" style="">
										<div class="col-12 text-title">Tình trạng công việc</div>
										<div class="col-12 text-info-mobile" :class="searchResultEmployee.status_user === 'active' ? 'text-success' : 'text-danger'">
											{{ searchResultEmployee.status_user === 'active' ? 'Đang hoạt động' : 'Đã nghỉ việc' }}
										</div>
									</div>
									<div class="row card-list-mobile" style="">
										<div class="col-12 text-title">Ghi chú</div>
										<div class="col-12 text-info-mobile" v-html="formatNote(searchResultEmployee.note)">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row" style="text-align: center">
							<p
								class="col-12"
								@click="reLookUpEmployee"
								style="cursor: pointer; color: #00507c; font-style: italic"
							>
								Tra cứu nhân viên khác
							</p>
						</div>
					</div>

					<div class="footer-component">
						<div class="d-flex justify-content-center">
							<span class="col-2"></span>
							<hr class="col-8" />
							<span class="col-2"></span>
						</div>
						<div class="col-12 justify-content-center mt-n4" style="display: flex">
							<span class="powered-title">
								Powered by
								<img src="@/assets/images/company_logo.png" height="15" />
								Fastvalue
							</span>
						</div>
					</div>
					<div class="popup-camera" v-if="isScanQR">
						<div class="card" style="height: 100vh">
							<div
								class="d-flex flex-column justify-content-center align-items-center"
							>
								<div class="img-company mb-3 mt-3">
									<img
										v-if="
											infoCompany.linkImage === '' ||
											infoCompany.linkImage === undefined ||
											infoCompany.linkImage === null
										"
										class="w-100"
										src="@/assets/icons/ic_company.png"
										alt="avatar"
									/>
									<img
										v-if="
											infoCompany.linkImage !== '' &&
											infoCompany.linkImage !== undefined &&
											infoCompany.linkImage !== null
										"
										class="w-100"
										:src="infoCompany.linkImage"
										alt="img"
									/>
								</div>
								<div class="row">
									<span class="col-2"></span>
									<span class="col-8 name-company">
										{{
											infoCompany.companyName
												? infoCompany.companyName.toUpperCase()
												: ""
										}}</span
									>
									<span class="col-2"></span>
								</div>
							</div>
							<div class="contain-camera mt-3">
								<StreamBarcodeReader
									torch
									no-front-cameras
									@decode="onDecode"
									@loaded="onLoaded"
								></StreamBarcodeReader>
							</div>
						</div>
					</div>
				</div>
			</a-tab-pane>
	</a-tabs>
	</div>
</template>

<script>
import moment from "moment";
import InputText from "@/components/Form/InputText";
import AppraiserCompany from "@/models/AppraiserCompany";
import ModalNotificationQRCode from "@/components/Modal/ModalNotificationQRCode.vue";
import { Tabs } from "ant-design-vue";

export default {
	name: "CertificateLookupIndex",
	data() {
		return {
			activeTab: 'chung-thu',
			message:
				"Vui lòng sử dụng camera trên điện thoại để quét mã QR trên chứng thư",
			infoCompany: {
				linkImage: "",
				companyName: "",
			},
			infoSearch: {
				certificate_id: "",
				certificate_num: "",
			},
			infoSearchEmployee: {
				// name: "",
				phone: "",
			},
			isScanQR: false,
			searchResult: {
				certificate_num: "010/2024/D07 - 0054/2",
				certificate_date: "2024-10-08",
				petitioner_name: "Ông TRỊNH ĐỨC DŨNG",
				appraises: [
					{
						id: 1,
						appraise_asset:
							"Thửa đất 180, tờ 30, Thôn Bãi Giếng 2, Xã Cam HảI Tây, Huyện Cam Lâm, Tỉnh Khánh Hòa",
					},
					{
						id: 2,
						appraise_asset:
							"Thửa đất 180, tờ 30, Thôn Bãi Giếng 2, Xã Cam HảI Tây, Huyện Cam Lâm, Tỉnh Khánh Hòa",
					},
				],
				appraise_purpose: "Vay vốn ngân hàng",
				asset_price: 2110000000,
			},
			searchResultEmployee: {
				name: "",
				phone: "",
				status_user: 'active',
				appraiser_number: '',
				appraise_position: {
					description: "",
				},
				branch: {
					name: "",
				},
				note: ""
			
			},
			isHaveResult: false,
			isHaveResultEmployee: false,
		};
	},
	components: {
		InputText,
		ModalNotificationQRCode,
		"a-tabs": Tabs,
		"a-tab-pane": Tabs.TabPane,
	},
	async created() {},
	beforeMount() {
		this.getCompanies();
	},
	mounted() {
		document.getElementById("app").style.backgroundColor =
			"rgba(247, 247, 247, 1)";

		if (
			this.$route &&
			this.$route.query &&
			Object.keys(this.$route.query).length !== 0
		) {
			if(this.$route.query.type === 'chung-thu'){
				this.activeTab = 'chung-thu'
				if (this.$route.query.id_chung_thu) {
					this.infoSearch.certificate_id = decodeURIComponent(
						this.$route.query.id_chung_thu
					);
				}
				if (this.$route.query.so_chung_thu) {
					this.infoSearch.certificate_num = decodeURIComponent(
						this.$route.query.so_chung_thu
					);
				}
				if (this.infoSearch.certificate_id && this.infoSearch.certificate_num) {
					this.lookupCertificate();
				}
			} else {
				this.activeTab = 'nhan-vien';
				if (this.$route.query.phone) {
					this.infoSearchEmployee.phone = decodeURIComponent(
						this.$route.query.phone
					);
				}
				if (this.infoSearchEmployee.phone) {
					this.lookUpEmployee();
				}
			}
		} else {
			this.$router.push({
				query: {
					...this.$route.query,
					type: 'chung-thu',
				},
			});
			this.activeTab = 'chung-thu';
		}
	},
	computed: {
		isValidInfoSearch() {
			if (this.infoSearch.certificate_id && this.infoSearch.certificate_num) {
				return true;
			} else {
				return false;
			}
		},
		isValidInfoSearchEmployee() {
			if (this.infoSearchEmployee.phone) {
				return true;
			} else {
				return false;
			}
		},

	},

	methods: {
		formatNote(note) {
			if (!note) return '';
			return note.replace(/\n/g, '<br>');
		},
		onChangeTab(event){
			this.activeTab = event;
			if (event === 'chung-thu') {
				this.$router.replace({ name: "certificate_lookup", query: {type:'chung-thu'} });
				// this.reLookUpEmployee();
			} else {
				this.$router.replace({ name: "certificate_lookup", query: {type:'nhan-vien'} });
				// this.reLookUp();
			}
		},
		calcTotalOtherAssets(other_assets) {
			let total = 0;
			if (other_assets && other_assets.length > 0) {
				for (let index = 0; index < other_assets.length; index++) {
					const element = other_assets[index];
					if(element){
						total += element.asset_price;
					}
				}
			}
			return total;
		},
		reLookUp() {
			this.infoSearch.certificate_id = "";
			this.infoSearch.certificate_num = "";
			this.isHaveResult = false;
			this.$router.replace({ name: "certificate_lookup", query: {type:'chung-thu'} });
			this.activeTab = 'chung-thu';
		},
		reLookUpEmployee() {
			this.infoSearchEmployee.phone = "";
			this.infoSearchEmployee.name = "";
			this.isHaveResultEmployee = false;
			this.$router.replace({ name: "certificate_lookup", query: {type:'nhan-vien'} });
			this.activeTab = 'nhan-vien';
		},
		formatDate(value) {
			return moment(String(value)).format("DD/MM/YYYY");
		},
		formatCurrency(value) {
			if (value) {
				let num = (value / 1).toFixed(0).replace(".", ",");
				return num.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
			}
			return value;
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
		},
		getNameCompanyDesktop(nameCompany) {
			let parts = [];

			if (nameCompany.includes("CỔ PHẦN")) {
				parts = nameCompany.split("CỔ PHẦN");
				return [parts[0] + "CỔ PHẦN", parts[1].trim()];
			} else if (nameCompany.includes("TNHH")) {
				parts = nameCompany.split("TNHH");
				return [parts[0] + "TNHH", parts[1].trim()];
			}
			return [
				nameCompany
					? nameCompany
					: "CÔNG TY CỔ PHẦN CÔNG NGHỆ ĐỊNH GIÁ TÀI SẢN VIỆT NAM",
			];
		},
		async getCompanies(id) {
			const resp = await AppraiserCompany.detailNoAuth();
			const data = resp.data;
			if (data.length > 0) {
				this.infoCompany = {
					linkImage: data[0].link,
					companyName: data[0].name,
				};
			}
		},
		async lookupEmployee() {
			this.$loading.show();
			const resp = await AppraiserCompany.lookUpEmployee(
				this.infoSearchEmployee
			);
			const data = resp.data;
			if (data) {
				this.$loading.hide();
				this.searchResultEmployee.status_user = data.status_user;
				this.searchResultEmployee.appraiser_number = data.appraiser_number ? data.appraiser_number : "";
				this.searchResultEmployee.avatarUrl = data.image ? data.image : '~@/assets/icons/ic_user.svg';
				this.searchResultEmployee.note = data.note ? data.note : "";
				this.searchResultEmployee.name = data.name ? data.name : "";
				this.searchResultEmployee.phone = data.phone ? data.phone : "";
				this.searchResultEmployee.appraise_position = data.appraiser &&  data.appraiser.appraise_position ? data.appraiser.appraise_position : null; 
				this.searchResultEmployee.branch = data.appraiser && data.appraiser.appraiser_branch ? data.appraiser.appraiser_branch : null;
				this.isHaveResultEmployee = true;
			} else {
				this.$loading.hide();
				this.$toast.open({
					message: "Không tìm thấy thông tin nhân viên, vui lòng kiểm tra lại",
					type: "info",
					position: "top-right",
					duration: 2000,
				});
			}
		},
		async lookupCertificate() {
			this.$loading.show();
			const resp = await AppraiserCompany.lookUpCertificate(this.infoSearch);
			const data = resp.data;
			if (data.length > 0) {
				this.searchResult.certificate_num = data[0].certificate_code_replace
					? data[0].certificate_code_replace
					: data[0].certificate_num;
				this.searchResult.certificate_date = data[0]
					.date_certificate_code_replace
					? data[0].date_certificate_code_replace
					: data[0].certificate_date;
				this.searchResult.appraises = data[0].real_estate;
				this.searchResult.petitioner_name = data[0].petitioner_name;
				this.searchResult.petitioner_phone = data[0].petitioner_phone;
				this.searchResult.appraise_purpose = data[0].appraise_purpose.name;
				this.searchResult.asset_price = data[0].asset_price[0].value;
				this.searchResult.appraise_date = data[0].appraise_date;
				this.searchResult.appraiser = data[0].appraiser;
				if(data[0].other_assets){
					const other_assets = JSON.parse(data[0].other_assets);
					if(other_assets && other_assets.length > 0){
						this.searchResult.other_assets = other_assets;
					}
				}
				this.$loading.hide();
				this.isHaveResult = true;
			} else {
				this.$loading.hide();
				this.$toast.open({
					message: "Không tìm thấy thông tin chứng thư, vui lòng kiểm tra lại",
					type: "info",
					position: "top-right",
					duration: 2000,
				});
			}
		},
		async onDecode(result) {
			if (result) {
				const params = result.split("?")[1];
				const paramArray = params.split("&");
				paramArray.forEach((param) => {
					const [key, value] = param.split("=");
					if (key === "id_chung_thu") {
						this.infoSearch.certificate_id = value;
					} else if (key === "so_chung_thu") {
						this.infoSearch.certificate_num = value;
					}
				});
				this.isScanQR = false;
				await this.lookupCertificate();
			}
		},
		onLoaded(result) {
		},
		scanQR() {
			this.isScanQR = true;
		},
		handleHome() {},
	},
};
</script>

<style lang="scss" scoped>
.text-title {
	color: rgba(125, 125, 125, 1);
	font-size: 1.5vh;
}

.text-custom-color {
	color: rgba(16, 163, 255, 1);
}

.card-result-desktop {
	box-shadow: 2px 2px 7px 0px rgba(247, 247, 247, 1);
	border-radius: 22px;
	padding-top: 2vh;
	padding-bottom: 2vh;
}
.card-list-desktop {
	padding: 1vh;
}
.card-result-mobile {
	box-shadow: 2px 2px 7px 0px rgba(247, 247, 247, 1);
	border-radius: 22px;
	padding: 2vh;
}
.card-list-mobile {
	padding: 0.5vh;
	padding-left: 1vh;
	padding-right: 1vh;
}
.text-align-right {
	text-align: right;
	font-weight: bold;
	font-size: 1.5vh;
}
.text-info-mobile {
	font-weight: bold;
	font-size: 1.5vh;
}
.base-line-desktop {
	border: 1px dashed rgba(231, 231, 231, 1);
	position: relative;
}
.base-line-desktop::before {
	content: "";
	display: block;
	width: 2vw; 
	height: 50px; 
	border-radius: 0 50px 50px 0; 
	background-color: rgba(247, 247, 247, 1); 
	position: absolute;
	top: -25px; 
}

.base-line-desktop::after {
	content: "";
	display: block;
	width: 2vw;
	height: 50px; 
	border-radius: 50px 0 0 50px; 
	background-color: rgba(247, 247, 247, 1);
	position: absolute;
	top: -25px;
}

.base-line-desktop::before {
	left: -2.2vw; 
}

.base-line-desktop::after {
	right: -2.2vw; 
}

.base-line-mobile {
	border: 1px dashed rgba(231, 231, 231, 1);
	position: relative;
}
.base-line-mobile::before {
	content: "";
	display: block;
	width: 3vh; 
	height: 4vh; 
	border-radius: 0 50px 50px 0; 
	background-color: rgba(247, 247, 247, 1); 
	position: absolute;
	top: -15px; 
}

.base-line-mobile::after {
	content: "";
	display: block;
	width: 3vh; 
	height: 4vh; 
	border-radius: 50px 0 0 50px; 
	background-color: rgba(247, 247, 247, 1); 
	position: absolute;
	top: -15px; 
}

.base-line-mobile::before {
	left: -7.2vw;
}

.base-line-mobile::after {
	right: -7.2vw; 
}

.img-company {
	display: flex;
	justify-content: center;
	width: 50px;
	height: 50px;
	img {
		object-fit: contain;
	}
}
.img-company-desktop {
	display: flex;
	justify-content: center;
	width: 10vh;
	height: 10vh;
	img {
		object-fit: contain;
	}
}
.name-company {
	display: flex;
	justify-content: center;
	text-align: center;
	font-size: 0.8rem;
	font-weight: 550;
	color: #00507c;
}
.name-company-desktop {
	display: flex;
	justify-content: center;
	text-align: center;
	font-size: 3vh;
	font-weight: 550;
	color: #00507c;
}
.space-component {
	height: 8vh;
}
.title-component {
}
.title-name {
	text-align: center;
	font-size: 1.2rem;
	font-weight: bold;
	color: black;
}
.title-name-desktop {
	text-align: center;
	font-size: 4vh;
	font-weight: bold;
	color: black;
}
.button-search {
	border-radius: 2.5rem;
	background-color: #00507c;
	color: white;
	font-weight: 100;
	font-size: 0.9rem;
}
.button-search-desktop {
	height: auto !important;
	border-radius: 2.5rem;
	background-color: #00507c;
	color: white;
	font-weight: 100;
	font-size: 2vh;
}
.other-component {
}
.other-title {
	text-align: center;
	font-size: 0.6rem;
	font-weight: 200;
}
.qr-title {
	text-align: center;
	font-size: 0.9rem;
	font-weight: 200;
}
.powered-title {
	text-align: center;
	font-size: 0.6rem;
	font-weight: 200;
}
.popup-camera {
	position: absolute;
	top: 0;
	z-index: 1000;
}
.contain-camera {
}
.footer-component {
	padding: 20px;
	text-align: center;
	position: absolute;
	bottom: 0;
	width: 100%;
}
.footer-component-desktop {
	padding-bottom: 1vh;
	text-align: center;
	position: absolute;
	bottom: 0;
	width: 100%;
}
.center {
	position: absolute;
	margin-top: 10rem;
	width: 4rem;
	height: 4rem;
	transform: translate(-50%, -50%);
	img {
		position: absolute;
		top: 75%;
		left: 50%;
		width: 4rem;
		height: 4rem;
		padding: 0.2rem;
		transform: translate(-50%, -50%);
		cursor: pointer;
	}
}

.square {
	position: absolute;
	top: 50%;
	left: 50%;
	width: 4rem;
	height: 3.8rem;
	padding: 0.2rem;
	background: transparent;
	transform: translate(-50%, -50%);
}

.scan {
	width: 100%;
	height: 0.6rem;
	background: linear-gradient(cyan, transparent);
	animation: scanning 1s linear alternate infinite;
}

@keyframes scanning {
	0% {
		transform: translatey(12px);
	}
	100% {
		transform: translatey(60px);
	}
}
.avatar-img {
	width: 120px;
  height: 120px;
  border-radius: 50%;
  img {
    object-fit: cover;
    border-radius: 50%;
  }
}
</style>