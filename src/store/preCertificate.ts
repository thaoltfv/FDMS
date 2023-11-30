// @ts-nocheck
import { defineStore } from 'pinia'
export const usePreCertificate = defineStore('preCertificate', () => {
  const data = ref({
    id: null,
    certificate_id: null,
    petitioner_name: null,
    petitioner_phone: null,
    petitioner_address: null,
    petitioner_identity_card: null,
    customer_id: null,
    pre_status_code: null,
    appraise_purpose_id: null,
    note: null,
    appraiser_sale_id: null,
    business_manager_id: null,
    appraiser_performance_id: null,
    total_preliminary_value: null,
    cancel_reason: null,
  })

  const preCertificateOtherDocuments = ref({
    pre_certificate_id: null,
    name: null,
    link: null,
    type: null,
    size: null,
    description: null,
    type_document: null,

  })

  function resetData() {
    data.value = {
      id: null,
      certificate_id: null,
      petitioner_name: null,
      petitioner_phone: null,
      petitioner_address: null,
      petitioner_identity_card: null,
      customer_id: null,
      pre_status_code: null,
      appraise_purpose_id: null,
      note: null,
      appraiser_sale_id: null,
      business_manager_id: null,
      appraiser_performance_id: null,
      total_preliminary_value: null,
      cancel_reason: null,
    }
  }

  return {
    data,
    resetData,
  }
}, {
  persist: true,
},)
if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(usePreCertificate, import.meta.hot))
}
