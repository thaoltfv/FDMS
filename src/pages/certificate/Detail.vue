<template>
  <div class="container-fluid" v-if="((typeof form !== 'undefined')&&(typeof form.id !== 'undefined'))">
    <div class="contain-detail">
      <div class="loading" :class="{'loading__true': isSubmit}">
        <a-spin />
      </div>
      <div class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title text-nowrap">Thông tin chung về tài sản thẩm định</h3>
            <img class="img-dropdown" :class="!showInfo? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showInfo = !showInfo">
          </div>
        </div>
        <div class="card-body card-info" v-if="showInfo">
          <div class="d-grid">
            <div class="content-detail content-code">
              <p class="content-title">Mã tin đăng:</p>
              <p class="content-name content-name__code">TSTD_{{form.id}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Trạng thái:</p>
              <p class="content-name" v-if="form.status === 1">Nháp</p>
              <p class="content-name" v-if="form.status === 2">Đã xác nhận</p>
              <p class="content-name" v-if="form.status === 3">Đã được chọn</p>
              <p class="content-name" v-if="form.status === 4">Hoàn thành</p>
              <p class="content-name" v-if="form.status === 5">Đã Hủy</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Loại tài sản</p>
              <p class="content-name">{{form.asset_type ? form.asset_type.description : ''}}</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Tỉnh/Thành:</p>
              <p class="content-name">{{form.province ? form.province.name : 'Chưa có tỉnh'}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Quận/Huyện:</p>
              <p class="content-name">{{form.district ? form.district.name : 'Chưa có quận'}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Phường/Xã:</p>
              <p class="content-name">{{form.ward ? form.ward.name : 'Chưa có xã'}}</p>
            </div>
          </div>
          <div class="d-grid">
                <div class="content-detail">
                  <p class="content-title">Đường:</p>
                  <p class="content-name">{{form.street ? form.street.name: 'Chưa có đường'}}</p>
                </div>
                <div class="content-detail" >
                  <p class="content-title">Đoạn:</p>
                  <p class="content-name">{{form.distance ? form.distance.detail : 'Chưa có đoạn'}}</p>
                </div>
                <div class="content-detail">
                  <p class="content-title">Tọa độ:</p>
                  <p class="content-name">{{form.coordinates ? form.coordinates : ''}}</p>
                </div>
          </div>
          <div class="d-grid--two">
            <!-- <div class="content-detail">
              <p class="content-title">Số tờ cũ:</p>
              <p class="content-name">{{form.doc_no_old ? this.form.doc_no_old : 'Trống'}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Số thửa cũ:</p>
              <p class="content-name">{{form.land_no_old ? this.form.land_no_old : 'Trống'}}</p>
            </div> -->
            <div class="content-detail">
              <p class="content-title">Tài sản thẩm định giá:</p>
              <p class="content-name">{{form.appraise_asset ? form.appraise_asset : 'Không xác định'}}</p>
            </div>
            <div class="content-detail" >
              <p class="content-title">Địa hình:</p>
              <p class="content-name">{{form.topographic ? this.form.topographic.description : 'Chưa có địa hình'}}</p>
            </div>
          </div>

        </div>
      </div>

      <div class="card" v-if="form.properties && form.properties.length > 0">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <h3 class="title">Thông tin về quyền sử dụng đất</h3>
            </div>
              <img class="img-dropdown" :class="!showLand? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showLand = !showLand">
          </div>
        </div>
        <div class="card-body card-info card-land" v-if="showLand">
          <div class="contain-table">
            <table class="table-property">
              <thead>
              <tr>
                <th>Mã số</th>
                <th>Tọa độ</th>
                <th>Loại đất</th>
                <th>Diện tích (m<sup>2</sup>)</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for = "(property) in form.properties" :key="property.id">
                <td>
                  <div class="input-code" @click="openModalDetail(property)">
                    TSD_{{property.id}}
                  </div>
                </td>
                <td>
                  {{property.coordinates}}
                </td>
                <td>
                  {{property.land_type ? property.land_type.description : ''}}
                </td>
                <td style="white-space: nowrap" >
                  <div>
                    {{property.appraise_land_sum_area}}
                  </div>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    <div class="card" v-if="form.tangible_assets && form.tangible_assets.length > 0">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin về công trình xây dựng</h3>
          <img class="img-dropdown" :class="!showTable? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showTable =! showTable">
        </div>
      </div>
      <div class="card-body card-info card-land" v-if="showTable">
        <div class="contain-table">
          <table class="table-property">
            <thead>
            <tr>
              <th>Mã số</th>
              <th>Loại</th>
              <th>Cấp nhà</th>
              <th>Chất lượng còn lại %</th>
              <th>Diện tích sàn (m<sup>2</sup>)</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for = "(tangible_asset) in form.tangible_assets" :key="tangible_asset.id">
              <td>
                <div class="input-code" @click="openTangibleDetail(tangible_asset)">
                  TSN_{{tangible_asset.id}}
                </div>
              </td>
              <td style="white-space: nowrap">
                {{tangible_asset.building_type !== undefined && tangible_asset.building_type !== null? tangible_asset.building_type.description : ''}}
              </td>
              <td style="white-space: nowrap">
                {{tangible_asset.building_category !== undefined && tangible_asset.building_category !== null ? tangible_asset.building_category.description : ''}}
              </td>
              <td>{{tangible_asset.remaining_quality + ' %'}}</td>
              <td>{{tangible_asset.total_construction_base}}m<sup>2</sup></td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="card" v-if="form.other_assets && form.other_assets.length > 0">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin về Tài sản khác</h3>
          <img class="img-dropdown" :class="!showOther? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showOther = !showOther">
        </div>
      </div>
      <div class="card-body card-info card-land" v-if="showOther">
        <div class="contain-table">
          <table class="table-property table-property__order">
            <thead>
            <tr>
              <th>Mã số</th>
              <th>Tên tài sản</th>
              <th>Đặc điểm</th>
              <th>Số lượng</th>
              <th>ĐVT</th>
              <th>Đơn giá</th>
              <th>Thành tiền</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for = "(other_asset) in form.other_assets" :key="other_asset.id">
              <td>
                <div class="input-code" style="cursor: default">
                  PL_{{other_asset.id}}
                </div>
              </td>
              <td>
                  {{other_asset.name ? other_asset.name : ''}}
              </td>
              <td>
                  {{other_asset.description ? other_asset.description : ''}}
              </td>
              <td>
                {{other_asset.total ? other_asset.total : ''}}
              </td>
              <td>
                {{other_asset.dvt ? other_asset.dvt : ''}}
              </td>
                <td>
                  {{other_asset.unit_price ? format(other_asset.unit_price) : 0}} đ
              </td>
                <td>
                  {{other_asset.unit_price ? format(other_asset.total_price) : 0}} đ
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="card" v-if="form.appraise_law && form.appraise_law.length > 0">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Pháp lý tài sản</h3>
          <img class="img-dropdown" :class="!showLaw? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showLaw = !showLaw">
        </div>
      </div>
      <div class="card-body card-info card-land" v-if="showLaw">
        <div class="contain-table">
          <table class="table-property table-property__order">
            <thead>
            <tr>
              <th>Mã số</th>
              <th>Tên pháp lý</th>
              <th>Số/Ngày</th>
              <th>Nội dung</th>
              <th>Cơ quan cấp, xác nhận</th>
              <!-- <th>Nguồn gốc sử dụng</th>
              <th>Quy hoạch</th> -->
            </tr>
            </thead>
            <tbody>
            <tr v-for="(law) in form.appraise_law" :key="law.id">
              <td>
                <div class="input-code" @click="openModalLegalDetail(law)">
                  PL_{{law.id}}
                </div>
              </td>
              <td>
                <p :id="`namelaw${law.id}`" class="name_law">{{!law.description && law.law ? law.law.content : law.description}}</p>
                <b-tooltip :target="('namelaw' + law.id).toString()">{{ law.description ? law.description : law.law.content }}</b-tooltip>
              </td>
              <td>
                <div style="min-width: 150px">{{law.date ? law.date : ''}}</div>

              </td>
              <td>
                <p :id="`content${law.id}`" class="content_law">{{law.content ? law.content : ''}}</p>
                <b-tooltip :target="('content' + law.id).toString()">{{ law.content }}</b-tooltip>
              </td>
              <td>

                <p :id="`certifyingAgency${law.id}`" class="certifying_agency">{{law.certifying_agency ? law.certifying_agency : ''}}</p>
                <b-tooltip :target="('certifyingAgency' + law.id).toString()">{{law.certifying_agency ? law.certifying_agency : ''}}</b-tooltip>
              </td>
              <!-- <td>
                {{law.origin_of_use ? law.origin_of_use : ''}}
              </td> -->
              <!-- <td>
                {{law.law_details && law.law_details.find(law_detail=> law_detail.is_zoning === true) ? 'Có' : 'Không'}}
              </td> -->
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title text-nowrap">Cơ sở giá trị, cách tiếp cận và phương pháp thẩm định</h3>
          <img class="img-dropdown" :class="!showAppraiserInfo? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showAppraiserInfo = !showAppraiserInfo">
        </div>
      </div>
        <Tabs :theme="theme" :navAuto="true">
          <TabItem name="Phương pháp tính toán">
            <div class="card-body card-info" v-if="showAppraiserInfo">
              <div class="d-grid d-grid--two">
                <div class="content-detail">
                  <p class="content-title">Thống nhất mức giá chỉ dẫn</p>
                  <p class="content-name ">{{form.unify_indicative_price ? form.unify_indicative_price.description : ''}}</p>
                </div>
              </div>
              <div class="d-grid d-grid--two">
                <div class="content-detail">
                  <p class="content-title">Tính giá đất hỗn hợp còn lại</p>
                  <p class="content-name ">{{form.composite_land_remaning ? form.composite_land_remaning.description : ''}}</p>
                </div>
                <div v-if="form.composite_land_remaning && form.composite_land_remaning.slug === 'theo-ty-le-gia-dat-co-so-chinh'" class="content-detail">
                  <p class="content-title">Tỷ lệ</p>
                  <p class="content-name ">{{form.composite_land_remaning_value ? `${form.composite_land_remaning_value} %` : ''}}</p>
                </div>
              </div>
              <div class="d-grid d-grid--two">
                <div class="content-detail">
                  <p class="content-title">Tính giá đất vi phạm quy hoạch</p>
                  <p class="content-name ">{{form.planning_violation_price ? form.planning_violation_price.description : ''}}</p>
                </div>
                <div v-if="form.planning_violation_price && form.planning_violation_price.slug === 'theo-ty-le-gia-dat-thi-truong'" class="content-detail">
                  <p class="content-title">Tỷ lệ</p>
                  <p class="content-name ">{{form.planning_violation_price_value ? `${form.planning_violation_price_value} %` : ''}}</p>
                </div>
              </div>
            </div>
          </TabItem>
          <TabItem name="Cơ sở giá trị và cách tiếp cận">
            <div class="card-body card-info" v-if="showAppraiserInfo">
              <div class="d-grid d-grid--two">
                <div class="content-detail">
                  <p class="content-title">Cơ sở giá trị của tài sản thẩm định giá:</p>
                  <p class="content-name ">{{form.appraise_basis_property ? form.appraise_basis_property.name : ''}}</p>
                </div>
                <div class="content-detail">
                  <p class="content-title">Nguyên tắc thẩm định:</p>
                  <p class="content-name ">{{form.appraise_principle ? form.appraise_principle.name : ''}}</p>
                </div>
              </div>
              <div class="d-grid d-grid--two">
                <div class="content-detail">
                  <p class="content-title">Cách tiếp cận:</p>
                  <p class="content-name ">{{form.appraise_approach ? form.appraise_approach.name : ''}}</p>
                </div>
                <div class="content-detail">
                  <p class="content-title">Phương pháp sử dụng:</p>
                  <p class="content-name ">{{form.appraise_method_used ? form.appraise_method_used.name : ''}}</p>
                </div>
              </div>
              <div class="content-detail">
                <p class="content-title">Giả thiết và giả thiết đặc biệt:</p>
                <p class="content-name">{{form.document_description ? form.document_description : ''}}</p>
              </div>
            </div>
          </TabItem>
        </Tabs>
    </div>

    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Đơn vị xây dựng</h3>
          <img class="img-dropdown" :class="!showCardCompany ? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardCompany = !showCardCompany">
        </div>
      </div>
      <div class="card-body card-info" v-show="showCardCompany">
        <div class="container-content__detail row align-items-center mb-0">
          <div class="container-content__title col-3 mb-0">Chọn đơn vị xây dựng</div>
          <div class="col d-flex align-items-center">
            <button type="button" class="btn btn-orange" @click="handleOpenModalCompany">Thông tin chi tiết</button>
            <div class="text-danger ml-2">
              Đã chọn {{form.construction_company.length}} đơn vị
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title text-nowrap">Hình ảnh</h3>
          <img class="img-dropdown" :class="!showImage? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showImage = !showImage">
        </div>
      </div>
      <div class="card-body" v-show="showImage">
      <Tabs :theme="theme" :navAuto="true">
        <TabItem name="Đường tiếp giáp tài sản">
          <div class="mb-2">
            <!-- <h3>Đường tiếp giáp tài sản thẩm định giá</h3> -->
            <div class="container-img row mr-0 ml-0">
              <div class="img-empty text-center" v-if="form.pic.length === 0 || form.pic.find(image => image.pic_type.description.toLowerCase() === 'đường tiếp giáp tài sản thẩm định giá') === undefined">
                <img src="../../assets/images/img_emply.svg" alt="empty">
                <p class="empty-content"> Chưa có hình</p>
              </div>
              <div class="img-contain col-4 col-lg-2" v-for="images in form.pic" :key="images.id" @click="openModalImage(images)" v-if="images.pic_type.description.toLowerCase() === 'đường tiếp giáp tài sản thẩm định giá'">
                <img style="max-height: 100%;" :src="images.link" alt="img">
              </div>
            </div>
          </div>
        </TabItem>
        <TabItem name="Tổng thể tài sản">
          <div class="mb-2">
            <!-- <h3>Tổng thể tài sản thẩm định giá</h3> -->
            <div class="container-img row mr-0 ml-0">
              <div class="img-empty text-center" v-if="form.pic.length === 0 || form.pic.find(image => image.pic_type.description.toLowerCase() === 'tổng thể tài sản thẩm định giá') === undefined">
                <img src="../../assets/images/img_emply.svg" alt="empty">
                <p class="empty-content"> Chưa có hình</p>
              </div>
              <div class="img-contain col-4 col-lg-2" v-for="images in form.pic" :key="images.id" @click="openModalImage(images)" v-if="images.pic_type.description.toLowerCase() === 'tổng thể tài sản thẩm định giá'">
                <img style="max-height: 100%;" :src="images.link" alt="img">
              </div>
            </div>
          </div>
        </TabItem>
        <TabItem name="Hiện trạng tài sản">
          <div class="mb-2">
            <!-- <h3>Hiện trạng tài sản thẩm định giá</h3> -->
            <div class="container-img row mr-0 ml-0">
              <div class="img-empty text-center" v-if="form.pic.length === 0 || form.pic.find(image => image.pic_type.description.toLowerCase() === 'hiện trạng tài sản thẩm định giá') === undefined">
                <img src="../../assets/images/img_emply.svg" alt="empty">
                <p class="empty-content"> Chưa có hình</p>
              </div>
              <div class="img-contain col-4 col-lg-2" v-for="images in form.pic" :key="images.id" @click="openModalImage(images)" v-if="images.pic_type.description.toLowerCase() === 'hiện trạng tài sản thẩm định giá'">
                <img style="max-height: 100%;" :src="images.link" alt="img">
              </div>
            </div>
          </div>
        </TabItem>
        <TabItem name="Pháp lý tài sản">
          <div>
            <!-- <h3>Pháp lý tài sản</h3> -->
            <div class="container-img row mr-0 ml-0">
              <div class="img-empty text-center" v-if="form.pic.length === 0 || form.pic.find(image => image.pic_type.description.toLowerCase() === 'pháp lý tài sản') === undefined">
                <img src="../../assets/images/img_emply.svg" alt="empty">
                <p class="empty-content"> Chưa có hình</p>
              </div>
              <div class="img-contain col-4 col-lg-2" v-for="images in form.pic" :key="images.id" @click="openModalImage(images)" v-if="images.pic_type.description.toLowerCase() === 'pháp lý tài sản'">
                <img style="max-height: 100%;" :src="images.link" alt="img">
              </div>
            </div>
          </div>
        </TabItem>
      </Tabs>
      </div>
    </div>
      <div class="card" v-if="form.asset_general && form.asset_general.length > 0">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <h3 class="title">Tài sản so sánh</h3>
            </div>
            <img class="img-dropdown" :class="!showCard ? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
          </div>
        </div>
        <div class="card-body card-info" v-if="showCard">
          <div class="row justify-content-center" v-if="(typeof form.asset_general !== 'undefined')">
            <div class="col-12 col-lg-3 mt-2" v-for="(asset, index) in form.asset_general" :key="asset.id">
              <div class="container__property">
                <div class="property__detail d-flex justify-content-between">
                  <p class="name">Tài sản {{index + 1}}:</p>
                  <p class="content content__id">{{asset.migrate_status + '_' + asset.id}}</p>
                </div>
                <div class="property__detail d-flex justify-content-between">
                  <p class="name">Phiên bản:</p>
                  <!-- <p class="content">{{asset.version[0] ? asset.version[0].version : ''}}</p> -->
                  <p class="content">{{showVersionAsset(asset)}}</p>
                </div>
                <div class="property__detail d-flex justify-content-between">
                  <p class="name">Loại giao dịch:</p>
                  <p class="content" :class="asset.transaction_type === 51? 'color__blue': asset.transaction_type === 53 ? 'color__orange' : asset.transaction_type === 52 ? 'color__purple': 'color__green'">{{asset.transaction_type.description}}</p>
                </div>
                <div class="property__detail d-flex justify-content-between">
                  <p class="name">Vị trí:</p>
                  <p class="content">{{asset.properties[0].front_side === 0 ? 'Hẻm' : asset.properties[0].front_side === 1? 'Mặt tiền' : '' }}</p>
                </div>
                <div class="property__detail d-flex justify-content-between" v-if="asset.front_side === 0">
                  <p class="name">Bề rộng hẻm:</p>
                  <p class="content">{{asset.main_road_length ? formatArea(asset.main_road_length) + ' m' : '' }}</p>
                </div>
                <div class="property__detail d-flex justify-content-between">
                  <p class="name">Tổng diện tích:</p>
                  <p class="content">{{formatArea(asset.total_area)}} m<sup>2</sup></p>
                </div>
                <div class="property__detail d-flex justify-content-between">
                  <p class="name">Giá trị ước tính:</p>
                  <p class="content">{{format(asset.total_estimate_amount)}} đ</p>
                </div>
                <div v-for="(landTypePrice, index) in asset.land_type_purpose_price" :key="'landTypePrice' + index">
                  <div class="property__detail d-flex justify-content-between" v-for="(land_type, index) in type_purposes" :key="'land_type' + index" v-if="landTypePrice.id === land_type.id">
                    <p class="name">Loại đất:</p>
                    <p class="content">{{land_type.description}}</p>
                  </div>
                  <div class="property__detail d-flex justify-content-between">
                    <p class="name">Đơn giá:</p>
                    <p class="content">{{format(landTypePrice.price_land)}} đ</p>
                  </div>
                </div>
                <div class="property__detail d-flex justify-content-between">
                  <p class="mb-0 w-100 text-left">Địa chỉ: <span>{{asset.full_address}}</span></p>
                </div>
                <p class="popup-link" @click="openModalPropertyDetail(asset, index)">Xem chi tiết</p>
              </div>
            </div>
            <div v-if="form.pic.find(image => image.pic_type.description === 'HÌNH BẢN ĐỒ')" class="container-imageMap mt-4 col-9">
              <h3 class="title text-center mt-0">Hình ảnh bản đồ</h3>
              <div class="w-100 h-auto" v-for="images in form.pic" :key="images.id" v-if="images.pic_type.description === 'HÌNH BẢN ĐỒ'">
                <img :src="images.link" alt="img">
              </div>
            </div>
          </div>
      </div>
  </div>
  <div v-if="form.comparison_factor && form.comparison_factor.length > 0" class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Yếu tố so sánh</h3>
            <img class="img-dropdown" :class="!showFactor ? 'img-dropdown__hide' : ''" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showFactor = !showFactor">
          </div>
        </div>
        <div class="card-body card-info" v-show="showFactor">
          <div class="container-content__detail row align-items-center mb-0">
            <div class="container-content__title col-3 mb-0">Yếu tố so sánh</div>
            <div class="col d-flex align-items-center">
              <button type="button" class="btn btn-orange" @click="handleOpenModalSelection">Thông tin chi tiết</button>
              <div class="text-danger ml-2">
                Đã chọn {{comparison_factor_tem.length}} yếu tố so sánh
              </div>
            </div>
          </div>
        </div>
  </div>
  <div v-if="form.asset_general && form.asset_general.length > 0" id="document_appraisal">
      <DocumentCertificate
        ref="documentAppraisal"
        :idData="idData"
        :formData="form"
        :status="form.status"
        :checkRole="checkRole"
        @refresh_action="getAppraiserId"
        @changeRouteToEdit="changeRouteToEdit"
      />
  </div>
  <ModalConstructionUnitDetail
    v-if="openModalDocumentsCompany"
    @cancel="cancelDataCompany"
    :documents="documents"
    :title="title"
  />
  <ModalDocumentsDetail
    v-if="openModalDocuments"
    @cancel="cancelData"
    :documents="documents"
    :data="data"
    :title="title"
  />
  <ModalLandDetail
    v-if="openDetail"
    v-bind:property="this.property"
    @cancel="cancelProperty"
    :frontSideOptions="frontSideOptions"
    :twoSidesLandOptions="twoSidesLandOptions"
    :individualRoadOptions="individualRoadOptions"
  />
  <ModalBuildingDetail
    v-if="openTangible"
    v-bind:tangible="this.tangible"
    @cancel="cancelTangible"
  />
    <ModalComparatorDetailCert
      v-if="openModalDocumentsSelection"
      @cancel="cancelDataSelection"
      :documents="compareDocument"
      :title="title"
    />
  <ModalLegalDetail
    v-if="openLegal"
    :property="legal"
    @cancel="cancelLegal"
  />
  <ModalImage
    v-if="openImage"
    v-bind:image_detail ="image_detail.link"
    @cancel="openImage = false"
  />
  <ModalInfoDetail
    v-if="open_detail"
    @cancel="open_detail = false"
    :property="property"
    :propertyIndex="propertyIndex"
    :pic="pic"
  />
   <!-- <ModalNotification
    v-if="openNotification"
    v-bind:notification="message"
    @cancel="openNotification = false"
    @action="handleAction"
  /> -->
  <ModalNotificationAppraisal
    v-if="openNotification"
    @cancel="openNotification = false"
    v-bind:notification="message"
    @action="handleAction"
  />
  <ModalNotificationAppraisal
    v-if="openDuplicateData"
    @cancel="openDuplicateData = false"
    v-bind:notification="message"
    @action="handleActionDuplicate"
  />
  <ModalNotificationAppraisal
    v-if="openCancelAppraisal"
    @cancel="openCancelAppraisal = false"
    v-bind:notification="message"
    @action="handleActionCancelAppraise"
  />
  </div>
  <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
    <div class="d-md-flex d-block button-contain ">
      <!-- <div style="margin-right:20px">
        <button class="btn btn-white btn-orange" @click.prevent="handleDuplicate(idData)">
          <img class="img" src="../../assets/images/ic_copy.svg" alt="edit">
            Nhân bản
        </button>
      </div>
      <div style="margin-right:20px" v-if="(form.status === 2 || form.status === 1) && edit ? true : false ">
        <button class="btn btn-white" @click.prevent="handleCancelProperty(idData)">
          <img class="img" src="../../assets/icons/ic_destroy.svg" alt="edit">
            Hủy tài sản
        </button>
      </div> -->
        <div v-if="form.status === 1 && accept ? true : false">
          <button class="btn btn-white btn-orange" @click.prevent="handleApproveOpen(idData)">
           <img class="img" src="../../assets/icons/ic_done.svg" alt="edit">
            Xác nhận
          </button>
        </div>

      <div style="margin-left:20px" v-if="(form.status === 2 || form.status === 1) && edit && checkRole  ? true : false ">
        <button class="btn btn-white" @click.prevent="handleEdit(idData)">
          <img class="img" src="../../assets/icons/ic_edit.svg" alt="edit">
          Chỉnh sửa
        </button>
      </div>
      <!-- <b-button-group>
        <button style="margin-right: 2px; margin-left: 20px" class="btn btn-white" @click="onCancel" type="button">
          <img class="img" src="../../assets/icons/ic_cancel.svg" alt="cancel">Trở về
        </button>
        <b-dropdown class="btn_dropdown" right dropup>
          <b-dropdown-item @click="handleCancelProperty(idData)">
            <button style="width: 100%;" class="btn btn-white" @click.prevent="handleCancelProperty(idData)">
              <img class="img" src="../../assets/icons/ic_destroy.svg" alt="edit">Hủy tài sản
            </button>
          </b-dropdown-item>
          <b-dropdown-divider></b-dropdown-divider>
          <b-dropdown-item @click="handleDuplicate(idData)">
            <button style="width: 100%;" class="btn btn-white btn-orange" @click.prevent="handleDuplicate(idData)">
              <img class="img" src="../../assets/images/ic_copy.svg" alt="edit">Nhân bản
            </button>
          </b-dropdown-item>
          <b-dropdown-divider></b-dropdown-divider>
        </b-dropdown>
      </b-button-group> -->

      <b-button-group class="btn_group">
        <button style="margin-right: 2px" class="btn btn-white" @click="onCancel" type="button">
          <img class="img" src="../../assets/icons/ic_cancel.svg" alt="cancel">Trở về
        </button>
        <b-dropdown  class="btn_dropdown" right dropup>
          <b-dropdown-item  style="min-width:194px !important" class="duplicate_item dropdown_menu_btn_detail" @click="handleDuplicate(idData)">
            <div class="div_item_dropdown">
              <img style="height: 35px" class="img" src="../../assets/images/ic_copy.svg" alt="edit"> Nhân bản
            </div>
          </b-dropdown-item>
          <b-dropdown-item v-if="(form.status === 2 || form.status === 1) && edit ? true : false" @click="handleCancelProperty(idData)">
            <div class="div_item_dropdown">
              <img style="height: 35px" class="img" src="../../assets/icons/ic_destroy.svg" alt="edit">Hủy tài sản
            </div>
          </b-dropdown-item>
        </b-dropdown>
      </b-button-group>

    </div>
  </div>
</div>
</template>
<script>
import InputText from '@/components/Form/InputText'
import InputCategoryData from '@/components/Form/InputCategoryData'
import WareHouse from '@/models/WareHouse'
import ModalLandDetail from './components/modals/ModalLandDetail'
import ModalImage from '@/components/Modal/ModalImage'
import ModalBuildingDetail from './components/modals/ModalBuildingDetail'
import ModalDocumentsDetail from './components/modals/ModalDocumentsDetail'
import ModalConstructionUnitDetail from './components/modals/ModalConstructionUnitDetail'
import {BDropdown, BDropdownItem, BTooltip, BButtonGroup} from 'bootstrap-vue'
import moment from 'moment'
import ModalComparatorDetailCert from './components/modals/ModalComparatorDetailCert'
import AppraiseData from '@/models/AppraiseData'
import ModalInfoDetail from '@/pages/certificate/components/modals/ModalInfoDetail'
import ModalLegalDetail from '@/pages/certificate/components/modals/ModalLegalDetail'
import DocumentCertificate from './components/DocumentCertificate'
import ModalNotification from '@/components/Modal/ModalNotification'
import ModalNotificationAppraisal from '@/components/Modal/ModalNotificationAppraisal'
import Certificate from '@/models/Certificate'
import {Tabs, TabItem} from 'vue-material-tabs'

export default {
	name: 'Detail_Appraise',
	components: {
		InputText,
		ModalLandDetail,
		ModalBuildingDetail,
		ModalImage,
		'b-dropdown': BDropdown,
		'b-dropdown-item': BDropdownItem,
		'b-tooltip': BTooltip,
		'b-button-group': BButtonGroup,
		InputCategoryData,
		ModalDocumentsDetail,
		ModalConstructionUnitDetail,
		ModalInfoDetail,
		ModalLegalDetail,
		ModalComparatorDetailCert,
		DocumentCertificate,
		ModalNotification,
		ModalNotificationAppraisal,
		TabItem,
		Tabs
	},
	data () {
		return {
			theme: {
				navItem: '#000000',
				navActiveItem: '#FAA831',
				slider: '#FAA831',
				arrow: '#000000'
			},
			idData: null,
			checkRole: false,
			open_detail: false,
			version: '',
			versions: [],
			showFactor: true,
			dataComparison: [],
			frontSideOptions: {
				items: {
					preSelected: '',
					labels: [
						{ name: 'Yes', color: 'white' },
						{ name: '', color: 'white' },
						{ name: 'No', color: 'white' }
					]
				}
			},
			twoSidesLandOptions: {
				items: {
					preSelected: '',
					labels: [
						{ name: 'Yes', color: 'white' },
						{ name: '', color: 'white' },
						{ name: 'No', color: 'white' }
					]
				}
			},
			individualRoadOptions: {
				items: {
					preSelected: '',
					labels: [
						{ name: 'Yes', color: 'white' },
						{ name: '', color: 'white' },
						{ name: 'No', color: 'white' }
					]
				}
			},
			openDuplicateData: false,
			showImage: true,
			showCardCompany: true,
			showCard: true,
			showAppraiserInfo: true,
			showAppraiser: true,
			showLaw: true,
			output: null,
			openImage: false,
			openDetail: false,
			openTangible: false,
			openLegal: false,
			showInfo: true,
			showTable: true,
			showLand: true,
			showOther: true,
			showDeal: true,
			showBlock: true,
			showApartment: true,
			openPrint: false,
			isSubmit: false,
			printDetail: '',
			purpose_use_lands: [],
			basic_utilities: [],
			checkedId: [],
			comparison_factor_tem: [],
			form: {
				id: '',
				id_amount: '',
				status: '1',
				input_source: 'DONAVA',
				post_id: '',
				asset_type: {
					type: ''
				},
				created_by: '',
				province: '',
				district: '',
				ward: '',
				street: '',
				doc_num: '',
				ward_id: '',
				street_id: '',
				transaction_type: '',
				land_no: '',
				doc_no: '',
				full_address: '',
				coordinates: '',
				source: '',
				public_date: '',
				property_other: '',
				contact_phone: '',
				total_area: '1',
				total_construction_area: '1',
				total_construction_amount: '',
				total_amount: '1',
				total_area_amount: '1',
				average_land_unit_price: '1',
				contact_person: '',
				properties: [],
				tangible_assets: [],
				other_assets: [],
				pic: [
					{
						link: ''
					}
				]
			},
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false,
			documents: [],
			title: '',
			openModalDocuments: false,
			openModalDocumentsCompany: false,
			data: '',
			imageMap: null,
			type_purposes: [],
			pic: [],
			property: null,
			propertyIndex: null,
			legal: null,
			compareDocument: [],
			openModalDocumentsSelection: false,
			openNotification: false,
			openCancelAppraisal: false
		}
	},
	async created () {
		const permission = this.$store.getters.currentPermissions
		// fix_permission
		permission.forEach(value => {
			if (value === 'VIEW_PRICE') {
				this.view = true
			}
			if (value === 'ADD_PRICE') {
				this.add = true
			}
			if (value === 'EDIT_PRICE') {
				this.edit = true
			}
			if (value === 'DELETE_PRICE') {
				this.deleted = true
			}
			if (value === 'ACCEPT_PRICE') {
				this.accept = true
			}
		})
		if ('id' in this.$route.query && this.$route.name === 'certificate.detail') {
			if (this.$route.meta['detail']) {
				this.form = Object.assign(this.form, {
					...this.$route.meta['detail']
				})
				this.getProfiles()
				this.idData = this.form.id
				this.getAppraiserId(this.form.id, this.form.version[this.form.version.length - 1].version)
			} else {
				this.$router.push({name: 'page-not-found'})
			}
		} else {
		}
	},
	computed: {
		sortedArray: function () {
			function compare (a, b) {
				if (a.price_land > b.price_land) { return -1 }
				if (a.price_land < b.price_land) { return 1 }
				return 0
			}
			// eslint-disable-next-line vue/no-side-effects-in-computed-properties
			return this.purpose_use_lands.sort(compare)
		},
		optionsVersion () {
			return {
				data: this.versions,
				id: 'version',
				key: 'version'
			}
		}
	},
	methods: {
		changeRouteToEdit () {
			this.$router.push({
				name: 'certificate.edit',
				query: {
					id: this.idData
				},
				params: { is_edit_asset: true }
			}).catch(_ => {})
		},
		showVersionAsset (asset) {
			let versionTitle = ''
			this.form.appraise_has_assets.forEach(item => {
				if (item.asset_general_id === asset.id) {
					versionTitle = item.version
				}
			})
			return versionTitle
		},
		async getProfiles () {
			const profile = this.$store.getters.profile
			if (profile && profile.data.user.roles[0].name.slice(-5) === 'ADMIN') {
				this.activeStatus = true
			}
			if (this.form.created_by && profile.data.user.roles[0].pivot.model_id === this.form.created_by.id) {
				this.checkRole = true
			}
		},
		// function hủy tài sản
		async handleActionCancelAppraise () {
			let status = 5
			const res = await AppraiseData.updateStatusAppraise(this.form.id, status)
			if (res.data === 1) {
				await this.$toast.open({
					message: 'Hủy tài sản thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.openNotification = await false
				await this.$router.push({name: 'certificate.index'}).catch(_ => {})
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			}
		},
		// function nhân bản tài sản
		handleActionDuplicate () {
			let id = this.idData
			this.$router.push({
				name: 'certificate.create',
				params: {
					id: id
				}
			}).catch(_ => {})
		},
		// function xác nhận tài sản
		async handleAction () {
			// call api
			let status = 2
			const res = await AppraiseData.updateStatusAppraise(this.form.id, status)
			if (res.data === 1) {
				await this.$toast.open({
					message: 'Xác nhận tài sản thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.openNotification = await false
				await this.$router.push({name: 'certificate.index'}).catch(_ => {})
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			}
		},
		handleDuplicate () {
			this.openDuplicateData = true
			this.message = 'Bạn có muốn nhân bản tải sản này không ?'
		},
		handleApproveOpen () {
			this.openNotification = true
			this.message = 'Bạn có muốn kích hoạt tài sản này không ?'
		},
		handleCancelProperty () {
			this.openCancelAppraisal = true
			this.message = 'Bạn có muốn hủy tài sản này không ?'
		},
		handleOpenModalSelection () {
			this.title = 'Yếu tố so sánh'
			this.compareDocument = this.comparison_factor_tem
			this.openModalDocumentsSelection = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		cancelDataSelection () {
			this.openModalDocumentsSelection = false
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
		},
		async getDictionaryLand () {
			const resp = await WareHouse.getDictionariesLand()
			this.type_purposes = [...resp.data]
		},
		handleOpenModalExpertise () {
			this.title = 'Văn bản pháp luật về thẩm định giá'
			this.documents = this.form.appraise_documents_valuation
			this.data = 'valuation'
			this.openModalDocuments = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		handleOpenModalLands () {
			this.title = 'Văn bản pháp luật về đất đai'
			this.documents = this.form.appraise_documents_land
			this.data = 'land'
			this.openModalDocuments = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		handleOpenModalConstructs () {
			this.title = 'Văn bản pháp luật về xây dựng'
			this.documents = this.form.appraise_documents_construction
			this.data = 'construct'
			this.openModalDocuments = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		handleOpenModalLocal () {
			this.title = 'Văn bản pháp luật của địa phương'
			this.documents = this.form.appraise_documents_local
			this.data = 'local'
			this.openModalDocuments = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		handleOpenModalCompany () {
			this.title = 'Đơn giá xây dựng'
			this.documents = this.form.construction_company
			this.openModalDocumentsCompany = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		cancelData () {
			this.openModalDocuments = false
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
		},
		cancelDataCompany () {
			this.openModalDocumentsCompany = false
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
		},
		cancelTangible () {
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
			this.openTangible = false
		},
		cancelProperty () {
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
			this.openDetail = false
		},
		cancelLegal () {
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
			this.openLegal = false
		},
		async print (id) {
			this.isSubmit = true
			await AppraiseData.getPrint(id).then((resp) => {
				const file = resp.data
				if (file) {
					const fileLink = document.createElement('a')
					fileLink.href = file.url
					fileLink.setAttribute('download', file.file_name)
					document.body.appendChild(fileLink)
					fileLink.click()
					fileLink.remove()
					window.URL.revokeObjectURL(fileLink)
				}
				this.isSubmit = false
			}
			)
		},
		formatDate (value) {
			return moment(String(value)).format('DD/MM/YYYY')
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		formatArea (value) {
			let num = (value / 1).toFixed(2).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		openModalImage (data) {
			this.openImage = true
			this.image_detail = data
		},
		openModalDetail (data) {
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
			this.openDetail = true
			this.property = data
			if (data.front_side === 1) {
				this.frontSideOptions.items.preSelected = 'Yes'
				data.front_side_switch = true
			} else if (data.front_side === 0) {
				data.front_side_switch = false
				this.frontSideOptions.items.preSelected = 'No'
			} else {
				this.frontSideOptions.items.preSelected = ''
			}
			if (data.two_sides_land === true) {
				this.twoSidesLandOptions.items.preSelected = 'Yes'
			} else if (data.two_sides_land === false) {
				this.twoSidesLandOptions.items.preSelected = 'No'
			} else {
				this.twoSidesLandOptions.items.preSelected = ''
			}
			if (data.individual_road === 1) {
				this.individualRoadOptions.items.preSelected = 'Yes'
			} else if (data.individual_road === 0) {
				this.individualRoadOptions.items.preSelected = 'No'
			} else {
				this.individualRoadOptions.items.preSelected = ''
			}
		},
		openTangibleDetail (data) {
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
			this.openTangible = true
			this.tangible = data
		},
		openModalLegalDetail (data) {
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
			this.openLegal = true
			this.legal = data
		},
		onCancel () {
			return this.$router.push({name: 'certificate.index'})
		},
		sortArrayPropertyDetail () {
			let purpose_use_lands = []
			this.form.properties.forEach(property => {
				property.property_detail.forEach(property_detail => {
					purpose_use_lands.push(property_detail)
				})
				this.purpose_use_lands = purpose_use_lands
			})
		},
		async getPrint (id) {
			const resp = await WareHouse.getPrintPdf(id)
			this.printDetail = resp.data.url
		},
		async getDictionary () {
			try {
				const reps = await WareHouse.getDictionaries()
				this.basic_utilities = [...reps.data.tien_ich_co_ban]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async handleEdit (id) {
			this.$router.push({
				name: 'certificate.edit',
				query: {
					id: id
				}
			}).catch(_ => {})
		},
		handleCheck () {
			if (this.form.block_specification !== undefined && this.form.block_specification.length > 0) {
				this.form.block_specification[0].basic_utilities.forEach(item => {
					this.checkedId.push(
						item.id
					)
				})
			}
		},
		async getAppraiserId (id, version) {
			const res = await AppraiseData.getAppraiseID(id, version)
			const response = await Certificate.getDataComparison(id)
			if (typeof res.data !== 'undefined' && res !== null && res.data !== null) this.form = res.data
			this.dataComparison = await response.data
			this.comparison_factor_tem = []
			if (this.dataComparison && id in this.dataComparison) {
				this.dataComparison[id].forEach(item => {
					this.comparison_factor_tem.push({ name: item.label })
				})
			}
		},
		async getVersion () {
			const res = await WareHouse.getVersion(this.form.id)
			this.versions = [...res.data]
		},
		async getAssetGeneralDetail (id) {
			this.isSubmit = true
			const resp = await WareHouse.getAssetGeneralDetail(id)
			this.property = resp.data
			this.isSubmit = false
		},
		async openModalPropertyDetail (property, index) {
			await this.getAssetGeneralDetail(property.id)
			this.pic = property.pic
			this.propertyIndex = this.form.appraise_has_assets[index].asset_property_detail_id
			this.open_detail = true
		}
	},
	beforeRouteEnter: async (to, from, next) => {
		const certificate = await AppraiseData.find(to.query['id'])
		to.meta['detail'] = certificate.data
		return next()
	},
	beforeMount () {
		this.sortArrayPropertyDetail()
		this.getDictionary()
		this.handleCheck()
		this.getDictionaryLand()
	}
}
</script>
<style scoped lang="scss">
  .certifying_agency {
    width: 250px;
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
  .name_law {
    width: 250px;
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
  .content_law {
    @media (max-width: 1600px) {
      width: 450px;
    }
    @media (min-width: 1600px) {
      width: 600px;
    }
    @media (min-width: 1900px) {
      width: 900px;
    }

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
.contain-detail {
  margin-bottom: 80px;
  @media (max-width: 767px) {
    margin-bottom: 145px;
  }
}
.pannel {
  background: #FFFFFF;
  box-shadow: 1px 2px 0 #e5eaee;
  border-radius: 5px;
  padding: 25px;
  margin-bottom: 40px;

  &__table {
    padding: 25px 0;
    border-radius: 5px;
  }

  &__input {
    p {
      color: #5a5386;
      font-weight: 600;
    }
  }
}
.button-contain {
  @media (max-width: 418px) {
    display: flex !important;
    justify-content: space-between;
    flex-wrap: wrap;
  }
}
.img{
  margin-right: 13px;
  max-width: 20px;
}
.card{
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #FFFFFF;
  margin-bottom: 25px;
  @media (max-width: 418px) {
    margin-bottom: 20px;
  }
  &-title{
    background: #F3F2F7;
    padding: 16px 20px;
    margin-bottom: 0;
    @media (max-width: 418px) {
      padding: 15px;
    }
    .title{
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 0;
    }
  }
  &-body{
    padding: 35px 30px 40px;
    @media (max-width: 418px) {
      padding: 15px;
    }
  }
  &-info{
    .title{
      font-size: 1.125rem;
      font-weight: 700;
      margin-top: 28px;
      &-highlight {
        color: #333333;
        background: rgba(252, 194, 114, 0.53);
        text-align: center;
        padding: 10px 0;
        border-radius: 2px;
      }
    }
  }
  &-land{
    position: relative;
    padding: 0;
  }
}
.btn{
  &-white{
    max-height: none;

    line-height: 19.07px;
    margin-right: 15px;

    @media (max-width: 418px) {
      width: 45%;
      margin-right: 0;
    }
    &:last-child{
      margin-right: 0;
    }
  }
  &-contain{
    margin-bottom: 55px;
    @media (max-width: 418px) {
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
    }
  }
}
.d-grid{
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  grid-column-gap: 8.9%;
  &--two {
    grid-template-columns: 1fr 1fr;
  }
  @media (max-width: 767px) {
    grid-template-columns: 1fr;
  }
}
.content{
  &-detail{
    p {
      padding-right: 10px;
      &:nth-last-child(1) {
        padding-right: 0;
      }
    }
  }
  &-title{
    color: #555555;
    margin-bottom: 5px;

    font-weight: 500;
  }
  &-name{
    font-size: 1.125rem;
    color: #000000;
    margin-bottom: 15px;
    font-weight: 600;
    @media (max-width: 767px) {

    }
    &__code{
      color: #FAA831;
    }
  }
}
.title{
  margin-bottom: 35px;
}
.contain-table{
    overflow-x: auto;
  @media (max-width: 767px) {
    overflow-y: hidden;
  }
  .table-property{
    width: 100%;
  }
}
.table-property{
  width: 100%;
  font-weight: 500;
  color: #000000;
  text-align: center;
  thead{
    th{
      padding: 12px 0;
      font-weight: 500;
      @media (max-width: 418px) {
        padding: 12px;
      }
    }
  }
  tbody{
    td{
      border: 1px solid #E5E5E5;
      &:first-child{
        border-left: none;
        width: 180px
      }
      &:last-child{
        border-right: none;
      }
      box-sizing: border-box;
      padding: 14px;
    }
  }
}
.img-content{
  padding-left: 20px;
  color: rgba(0,0,0,0.85);
  font-size: 1.125rem;
  font-weight: 600;
  span{
    font-weight: 500;
    margin-left: 10px;
  }
}
.input-code{
  color: #FAA831;
  cursor: pointer;
}
.img-dropdown{
  cursor: pointer;
  width: 18px;
  &__hide{
    transform: rotate(90deg);
    transition: .3s;
  }
}
.img-contain {
  aspect-ratio: 1/1;
  overflow: hidden;
  @media (min-width: 64rem) {
    flex: 0 0 11%;
    width: 11%;
    padding: 25px 10px;
  }
  img{
    cursor: pointer;
    object-fit: cover;
    // height: 100%;
  }
  &__table{
    margin: auto;
    max-width: 50px;
    max-height: 50px;
    img{
      object-fit: cover;
      object-position: top;
      cursor: pointer;
      display: flex;
      justify-content: center;
      max-width: 50px;
      max-height: 50px;
    }
  }
}
.product-images {
  @media (max-width: 786px) {
    display: block !important;
    .img-contain {
      margin-bottom: 5px;
      max-width: 100%;
      max-height: 100%;
      img {
        width: 100%;
        max-width: 100%;
        max-height: 100%;
      }
    }
  }
}
.container-img{
  padding: .75rem 0;
  border: 1px solid #0b0d10;
}
.btn-footer {
  background: #FFFFFF;
  padding: 20px 30px;
  position: fixed;
  left: 0;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  bottom: 0;
  right: 0;
}
.dropdown-container{
  border-radius: 2px;
  background: #FAA831;
  img{
    padding: 7px;
  }
}
.loading{
  display: none;
  &__true{
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 100vh;
    background: rgba(0, 0, 0, 0.62);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
    &.btn-loading{
      &:after{
        width: 2rem !important;
        height: 2rem !important;
      }
    }
  }
}
.input-checkbox {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  input {
    width: 20px;
    height: 20px;
    position: absolute;
    cursor: pointer;
    opacity: 0;
    &:checked {
      & ~ .check-mark {
        background-color: #FAA831;
        &:after {
          display: block;
        }
      }
    }
  }
  .check-mark {
    position: absolute;
    top: 0;
    left: 0;
    cursor: pointer;
    width: 20px;
    height: 20px;
    background-color: #FFFFFF;
    border: 1px solid #FAA831;
    border-radius: 4px;
    &:after {
      content: "";
      position: absolute;
      display: none;
      left: 50%;
      top: 50%;
      width: 5px;
      height: 10px;
      border: solid #FFFFFF;
      border-width: 0 3px 3px 0;
      -webkit-transform: rotate(45deg) translate(-125%, -25%);
      -ms-transform: rotate(45deg) translate(-125%, -25%);
      transform: rotate(45deg) translate(-125%, -25%);
    }
  }
}
.mr-15{
  margin-right: 15px;
  @media (max-width: 767px) {
    margin-right: 0;
  }
}
.container-content {
  &__detail {
    margin-bottom: 20px;
  }
  &__title {
    font-size: 0.875rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #333333;
  }
}
.container {
  &__property {
    height: 100%;
    border: 1px solid #D0D0D0;
    padding: 15px;
    border-radius: 5px;
    @media (max-width: 1023px) {
      margin-bottom: 20px;
    }
    .property {
      &__detail{

        color: #000000;
        margin-bottom: 5px;
        .name, .content{
          margin-bottom: 0;
          padding: 0 !important;
        }
        .name {
          text-align: left;
          width: 50%;
          white-space: nowrap;
          text-overflow: ellipsis;
          overflow: hidden;
        }
        .content {
          color: #333333;
          display: block;
          text-align: end;
          &__id{
            color: #FAA831;
          }
        }
      }
    }
  }
}
.container-imageMap {
  border: 1px solid;
  padding: 10px;
}
.popup{
  &-link {
    text-align: right;
    color: #F28C1C !important;
    font-weight: 600;
    text-decoration-line: underline;
    cursor: pointer;
    margin-bottom: 0 !important;
  }
}
.btn_dropdown {
  box-shadow: 0 1px 4px rgba(0,0,0,0.25);
  border:white;
  border-radius: 5px;
}
.div_item_dropdown {
  padding: 0px 20%;
  width: 100%;
}
.duplicate_item {
  background-color: #FAA831;
  color: #FFFFFF;
}
.btn_group {
  margin-left:20px;
}
</style>
