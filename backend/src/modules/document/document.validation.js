const documentValidation = {
  listDocuments: {
    params: {
      type: 'object',
      required: ['blueprintCode'],
      properties: {
        blueprintCode: {
          type: 'string',
          minLength: 1,
          maxLength: 100
        }
      }
    },
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
          default: 10
        },
        sort: {
          type: 'string',
          maxLength: 63
        },
        order: {
          type: 'string',
          enum: ['asc', 'desc'],
          default: 'desc'
        },
        search: {
          type: 'string',
          maxLength: 255
        },
        stage: {
          type: 'string',
          maxLength: 50
        }
      }
    }
  },

  getDocument: {
    params: {
      type: 'object',
      required: ['blueprintCode', 'id'],
      properties: {
        blueprintCode: {
          type: 'string',
          minLength: 1,
          maxLength: 100
        },
        id: {
          type: 'integer',
          minimum: 1
        }
      }
    }
  },

  createDocument: {
    params: {
      type: 'object',
      required: ['blueprintCode'],
      properties: {
        blueprintCode: {
          type: 'string',
          minLength: 1,
          maxLength: 100
        }
      }
    },
    body: {
      type: 'object',
      additionalProperties: true // Allow dynamic fields based on blueprint
    }
  },

  updateDocument: {
    params: {
      type: 'object',
      required: ['blueprintCode', 'id'],
      properties: {
        blueprintCode: {
          type: 'string',
          minLength: 1,
          maxLength: 100
        },
        id: {
          type: 'integer',
          minimum: 1
        }
      }
    },
    body: {
      type: 'object',
      additionalProperties: true // Allow dynamic fields based on blueprint
    }
  },

  deleteDocument: {
    params: {
      type: 'object',
      required: ['blueprintCode', 'id'],
      properties: {
        blueprintCode: {
          type: 'string',
          minLength: 1,
          maxLength: 100
        },
        id: {
          type: 'integer',
          minimum: 1
        }
      }
    }
  },

  transitionStage: {
    params: {
      type: 'object',
      required: ['blueprintCode', 'id'],
      properties: {
        blueprintCode: {
          type: 'string',
          minLength: 1,
          maxLength: 100
        },
        id: {
          type: 'integer',
          minimum: 1
        }
      }
    },
    body: {
      type: 'object',
      required: ['target_stage'],
      properties: {
        target_stage: {
          type: 'string',
          minLength: 1,
          maxLength: 50
        },
        comment: {
          type: 'string',
          maxLength: 1000
        }
      }
    }
  },

  getDocumentVersions: {
    params: {
      type: 'object',
      required: ['blueprintCode', 'id'],
      properties: {
        blueprintCode: {
          type: 'string',
          minLength: 1,
          maxLength: 100
        },
        id: {
          type: 'integer',
          minimum: 1
        }
      }
    },
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
          maximum: 50,
          default: 10
        }
      }
    }
  },

  getDocumentVersion: {
    params: {
      type: 'object',
      required: ['blueprintCode', 'id', 'versionId'],
      properties: {
        blueprintCode: {
          type: 'string',
          minLength: 1,
          maxLength: 100
        },
        id: {
          type: 'integer',
          minimum: 1
        },
        versionId: {
          type: 'integer',
          minimum: 1
        }
      }
    }
  },

  restoreVersion: {
    params: {
      type: 'object',
      required: ['blueprintCode', 'id', 'versionId'],
      properties: {
        blueprintCode: {
          type: 'string',
          minLength: 1,
          maxLength: 100
        },
        id: {
          type: 'integer',
          minimum: 1
        },
        versionId: {
          type: 'integer',
          minimum: 1
        }
      }
    }
  },

  searchDocuments: {
    params: {
      type: 'object',
      required: ['blueprintCode'],
      properties: {
        blueprintCode: {
          type: 'string',
          minLength: 1,
          maxLength: 100
        }
      }
    },
    body: {
      type: 'object',
      properties: {
        query: {
          type: 'string',
          maxLength: 255
        },
        filters: {
          type: 'object',
          additionalProperties: true
        },
        dateRange: {
          type: 'object',
          properties: {
            field: {
              type: 'string',
              maxLength: 63
            },
            from: {
              type: 'string',
              format: 'date-time'
            },
            to: {
              type: 'string',
              format: 'date-time'
            }
          }
        },
        spatial: {
          type: 'object',
          properties: {
            field: {
              type: 'string',
              maxLength: 63
            },
            geometry: {
              type: 'object',
              additionalProperties: true
            }
          }
        },
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
        }
      }
    }
  },

  exportDocuments: {
    params: {
      type: 'object',
      required: ['blueprintCode'],
      properties: {
        blueprintCode: {
          type: 'string',
          minLength: 1,
          maxLength: 100
        }
      }
    },
    querystring: {
      type: 'object',
      properties: {
        format: {
          type: 'string',
          enum: ['csv', 'json'],
          default: 'csv'
        },
        fields: {
          type: 'string',
          maxLength: 1000
        }
      }
    }
  },

  getAnalytics: {
    params: {
      type: 'object',
      required: ['blueprintCode'],
      properties: {
        blueprintCode: {
          type: 'string',
          minLength: 1,
          maxLength: 100
        }
      }
    },
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

  bulkOperations: {
    params: {
      type: 'object',
      required: ['blueprintCode'],
      properties: {
        blueprintCode: {
          type: 'string',
          minLength: 1,
          maxLength: 100
        }
      }
    },
    body: {
      type: 'object',
      required: ['operation', 'document_ids'],
      properties: {
        operation: {
          type: 'string',
          enum: ['delete', 'update', 'transition']
        },
        document_ids: {
          type: 'array',
          items: {
            type: 'integer',
            minimum: 1
          },
          minItems: 1,
          maxItems: 100
        },
        data: {
          type: 'object',
          additionalProperties: true
        }
      }
    }
  }
}

export default documentValidation