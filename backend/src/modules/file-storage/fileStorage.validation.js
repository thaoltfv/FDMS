const fileStorageValidation = {
  uploadFile: {
    querystring: {
      type: 'object',
      properties: {
        blueprint_id: {
          type: 'integer',
          minimum: 1
        },
        document_id: {
          type: 'integer',
          minimum: 1
        },
        document_table_name: {
          type: 'string',
          maxLength: 100
        },
        field_code: {
          type: 'string',
          maxLength: 100
        }
      }
    }
  },

  uploadMultipleFiles: {
    querystring: {
      type: 'object',
      properties: {
        blueprint_id: {
          type: 'integer',
          minimum: 1
        },
        document_id: {
          type: 'integer',
          minimum: 1
        },
        document_table_name: {
          type: 'string',
          maxLength: 100
        },
        field_code: {
          type: 'string',
          maxLength: 100
        }
      }
    }
  },

  getFile: {
    params: {
      type: 'object',
      required: ['fileId'],
      properties: {
        fileId: {
          type: 'integer',
          minimum: 1
        }
      }
    }
  },

  downloadFile: {
    params: {
      type: 'object',
      required: ['fileId'],
      properties: {
        fileId: {
          type: 'integer',
          minimum: 1
        }
      }
    }
  },

  getThumbnail: {
    params: {
      type: 'object',
      required: ['fileId'],
      properties: {
        fileId: {
          type: 'integer',
          minimum: 1
        }
      }
    },
    querystring: {
      type: 'object',
      properties: {
        size: {
          type: 'string',
          pattern: '^\\d+x\\d+$',
          default: '150x150'
        }
      }
    }
  },

  deleteFile: {
    params: {
      type: 'object',
      required: ['fileId'],
      properties: {
        fileId: {
          type: 'integer',
          minimum: 1
        }
      }
    }
  },

  listFiles: {
    querystring: {
      type: 'object',
      properties: {
        page: {
          type: 'integer',
          minimum: 1,
          default: 1
        },
        limit: {
          type: 'integer',
          minimum: 1,
          maximum: 100,
          default: 20
        },
        blueprint_id: {
          type: 'integer',
          minimum: 1
        },
        document_id: {
          type: 'integer',
          minimum: 1
        },
        document_table_name: {
          type: 'string',
          maxLength: 100
        },
        field_code: {
          type: 'string',
          maxLength: 100
        },
        mime_type_filter: {
          type: 'string',
          maxLength: 50
        },
        search: {
          type: 'string',
          maxLength: 255
        }
      }
    }
  },

  updateFile: {
    params: {
      type: 'object',
      required: ['fileId'],
      properties: {
        fileId: {
          type: 'integer',
          minimum: 1
        }
      }
    },
    body: {
      type: 'object',
      properties: {
        metadata: {
          type: 'object',
          additionalProperties: true
        }
      }
    }
  },

  getUsageStats: {
    querystring: {
      type: 'object',
      properties: {
        period: {
          type: 'string',
          enum: ['7d', '30d', '90d'],
          default: '30d'
        }
      }
    }
  },

  cleanupOrphaned: {
    // Admin only - no additional validation needed
  }
}

export default fileStorageValidation