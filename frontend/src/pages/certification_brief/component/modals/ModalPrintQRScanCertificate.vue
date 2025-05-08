<template>
  <div class="modal-delete">
    <div class="card">
      <div class="card-header d-flex justify-content-end align-items-center">
        <img @click="handleCancel" src="@/assets/icons/ic_cancel-1.svg" alt="icon" />
      </div>

      <div class="preview-wrapper">
        <div id="printBody">
          <div class="certificate-layout">
            <div class="certificate-number">Số: {{ certificateNum }}</div>

            <div class="qr-code-wrapper">
              <QRCode ref="qrCode" :value="url" :size="151" class="qr-code" />
            </div>
            <div class="certificate-id">ID: {{ certificateId }}</div>

            <div class="info-box mx-auto">
              <div class="info-row">
                <div class="info-label" contenteditable="true">KHÁCH HÀNG YÊU CẦU</div>
                <div class="info-colon">:</div>
                <div class="info-value" contenteditable="true">{{ data.petitioner_name }}</div>
              </div>
              <div class="info-row">
                <div class="info-label" contenteditable="true">ĐỊA CHỈ</div>
                <div class="info-colon">:</div>
                <div class="info-value" contenteditable="true">{{ data.petitioner_address || '' }}</div>
              </div>
              <div class="info-row">
                <div class="info-label" contenteditable="true">TÀI SẢN THẨM ĐỊNH</div>
                <div class="info-colon">:</div>
                <div class="info-value" contenteditable="true">{{ assetTypes }}</div>
              </div>
              <div class="info-row">
                <div class="info-label" contenteditable="true">MỤC ĐÍCH THẨM ĐỊNH</div>
                <div class="info-colon">:</div>
                <div class="info-value" contenteditable="true">{{ data.appraise_purpose ? data.appraise_purpose.name :
                  '' }}</div>
              </div>
              <div class="info-row">
                <div class="info-label" contenteditable="true">VỊ TRÍ TỌA LẠC</div>
                <div class="info-colon">:</div>
                <div class="info-value" contenteditable="true">{{ locations }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card-footer footer-print">
        <button class="btn btn-orange mr-2" @click="shareQRCode">Chia sẻ</button>
        <button class="btn btn-orange" v-print="printObj" @click="statusPrint">Tải xuống</button>
      </div>
    </div>
  </div>
</template>

<script>
import QRCode from "qrcode.vue";
import print from "vue-print-nb";

export default {
  name: "ModalPrintQRScanCertificate",
  props: ["certificateId", "certificateNum", "data"],
  directives: { print },
  components: { QRCode },
  data() {
    return {
      url: "",
      printObj: {
        id: "printBody",
        popTitle: "",
      },
    };
  },
  computed: {
    assetTypes() {
      const list = [];
      try {
        if (this.data.other_assets) {
          const parsed = JSON.parse(this.data.other_assets);
          const types = new Set();

          parsed.forEach(item => {
            if (item.asset_type === 'BDS') types.add('Bất động sản');
            if (item.asset_type === 'DS') types.add('Động sản');
          });

          list.push(...types);
        } else if (this.data.document_type && this.data.document_type.length) {
          if (this.data.document_type.includes('DT')) list.push('Quyền sử dụng đất');
          if (this.data.document_type.includes('DCN')) list.push('Quyền sử dụng đất và công trình xây dựng');
          if (this.data.document_type.includes('CC')) list.push('Quyền sử dụng căn hộ');
        }
      } catch (e) {
        console.error('Invalid JSON in other_assets', e);
      }

      return list.join(', ');
    },
    locations() {
      const list = [];
      try {
        if (this.data.other_assets) {
          const parsed = JSON.parse(this.data.other_assets);
          parsed.forEach(item => {
            if (item.asset_name) list.push(item.asset_name);
          });
        } else if (this.data.real_estate && this.data.real_estate.length) {
          this.data.real_estate.forEach(item => {
            if (item.appraises && item.appraises.full_address) {
              list.push(item.appraises.full_address);
            } else if (item.apartment && item.apartment.full_address) {
              list.push(item.apartment.full_address);
            }
          });
        }
      } catch (e) {
        console.error('Invalid JSON in other_assets', e);
      }

      return list.join(', ');
    }
  },
  mounted() {
    const domain = window.location.origin;
    this.url = `${domain}/tracuu?id_chung_thu=${this.certificateId}&so_chung_thu=${this.certificateNum}&type=chung-thu`;

    this.scaleA4Preview();
    window.addEventListener("resize", this.scaleA4Preview);
    console.log("data", this.data);
  },
  beforeDestroy() {
    window.removeEventListener("resize", this.scaleA4Preview);
  },
  methods: {
    handleCancel() {
      this.$emit("cancel");
    },
    statusPrint() {
      this.printObj = {
        id: "printBody",
        popTitle: "TraCuuChungThu_" + this.certificateId,
      };
      document.title = this.printObj.popTitle;
    },
    shareQRCode() {
      const shareData = {
        title: "Chia sẻ mã QR",
        text: "Quét mã QR để tra cứu chứng thư",
        url: this.url,
      };
      if (navigator.share) {
        navigator.share(shareData).catch((err) => {
          console.error("Share failed:", err);
        });
      }
    },
    scaleA4Preview() {
      this.$nextTick(() => {
        const wrapper = document.querySelector(".preview-wrapper");
        const a4 = document.getElementById("printBody");
        if (!wrapper || !a4) return;
        if (window.matchMedia("print").matches) return;

        const availableHeight = wrapper.clientHeight;
        const a4Height = 1122; // ~297mm ở 96dpi
        let scale = availableHeight / a4Height;
        if (scale > 1) scale = 1;
        a4.style.transform = `scale(${scale})`;
      });
    },
  },
};
</script>

<style scoped lang="scss">
.modal-delete {
  position: fixed;
  z-index: 10002;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
}

.card {
  max-width: 1000px;
  width: 100%;
  background: #fff;
}

.card-header {
  padding: 8px;
  border-bottom: none;
  text-align: right;
}

.footer-print {
  display: flex;
  justify-content: flex-end;
  padding: 1rem 1.5rem;
  background: white;
}

.preview-wrapper {
  width: 100%;
  height: calc(100vh - 120px);
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: start;
  background: #f8f8f8;

  #printBody {
    width: 210mm;
    height: 297mm;
    background-image: url('~@/assets/images/bia_chung_thu.png');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: top center;
    transform-origin: top center;
    transition: transform 0.2s ease;
    position: relative;
    padding-top: 95mm;
    padding-left: 25mm;
    box-sizing: border-box;
    font-family: "Times New Roman", serif;
  }
}

.certificate-layout {
  width: 160mm;
  margin: 0 auto;
  text-align: center;
}

.certificate-number {
  font-size: 20pt;
  font-weight: bold;
  color: #002d5f;
  // margin-bottom: 5mm;
}
.certificate-id {
  font-size: 15pt;
  font-weight: bold;
  color: #002d5f;
  // margin-bottom: 5mm;
}

.qr-code-wrapper {
  display: flex;
  justify-content: center;
  // margin-bottom: 5mm;
}

.info-box {
  width: 100%;
  border: 1.5px solid black;
  padding: 10px;
  background: rgba(255, 255, 255, 0.85);
  text-align: left;

  .info-row {
    display: flex;
    align-items: flex-start;
    // margin-bottom: 8px;
    font-size: 12pt;
    font-weight: bold;
    text-transform: uppercase;
  }

  .info-label {
    font-weight: bold;
    text-transform: uppercase;
    width: 200px; // chỉnh chiều rộng label để tất cả text canh trái đều nhau
    flex-shrink: 0;
  }

  .info-colon {
    width: 10px;
    flex-shrink: 0;
  }

  .info-value {
    flex: 1;
    word-break: break-word;
    white-space: pre-wrap;
    text-transform: uppercase; // <-- IN HOA TOÀN BỘ
    font-weight: bold;
  }
}

// .qr-code {
//   width: 40mm !important;
//   height: 40mm !important;
// }

@media print {
  @page {
    size: A4 portrait;
    margin: 0;
  }

  body {
    margin: 0;
    padding: 0;
  }

  // .qr-code {
  //   width: 40mm !important;
  //   height: 40mm !important;
  // }

  #printBody {
    width: 210mm !important;
    height: 297mm !important;
    transform: none !important;
    page-break-before: avoid;
    page-break-after: avoid;
    page-break-inside: avoid;

    // ✅ thêm dòng này để giữ background
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    background-image: url('~@/assets/images/bia_chung_thu.png');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: top center;
    position: relative;
    padding-top: 95mm;
    padding-left: 25mm;
    box-sizing: border-box;
    font-family: "Times New Roman", serif;

  }
}
</style>