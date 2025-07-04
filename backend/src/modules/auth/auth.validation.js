const authValidation = {
  register: {
    body: {
      type: 'object',
      required: ['email', 'password', 'full_name'],
      properties: {
        email: {
          type: 'string',
          format: 'email',
          maxLength: 255
        },
        password: {
          type: 'string',
          minLength: 8,
          maxLength: 100
        },
        full_name: {
          type: 'string',
          minLength: 2,
          maxLength: 255
        }
      }
    },
    response: {
      200: {
        type: 'object',
        properties: {
          user: {
            type: 'object',
            properties: {
              id: { type: 'number' },
              uuid: { type: 'string' },
              email: { type: 'string' },
              full_name: { type: 'string' },
              created_at: { type: 'string' }
            }
          },
          token: { type: 'string' }
        }
      }
    }
  },

  login: {
    body: {
      type: 'object',
      required: ['email', 'password'],
      properties: {
        email: {
          type: 'string',
          format: 'email'
        },
        password: {
          type: 'string',
          minLength: 1
        }
      }
    },
    response: {
      200: {
        type: 'object',
        properties: {
          user: {
            type: 'object',
            properties: {
              id: { type: 'number' },
              uuid: { type: 'string' },
              email: { type: 'string' },
              full_name: { type: 'string' },
              roles: {
                type: 'array',
                items: { type: 'string' }
              },
              groups: {
                type: 'array',
                items: { type: 'string' }
              },
              last_login_at: { type: 'string' }
            }
          },
          token: { type: 'string' }
        }
      }
    }
  },

  refresh: {
    headers: {
      type: 'object',
      required: ['authorization'],
      properties: {
        authorization: {
          type: 'string',
          pattern: '^Bearer '
        }
      }
    },
    response: {
      200: {
        type: 'object',
        properties: {
          token: { type: 'string' }
        }
      }
    }
  },

  getProfile: {
    headers: {
      type: 'object',
      required: ['authorization'],
      properties: {
        authorization: {
          type: 'string',
          pattern: '^Bearer '
        }
      }
    },
    response: {
      200: {
        type: 'object',
        properties: {
          user: {
            type: 'object',
            properties: {
              id: { type: 'number' },
              uuid: { type: 'string' },
              email: { type: 'string' },
              full_name: { type: 'string' },
              roles: {
                type: 'array',
                items: { type: 'string' }
              },
              groups: {
                type: 'array',
                items: { type: 'string' }
              }
            }
          }
        }
      }
    }
  },

  updateProfile: {
    headers: {
      type: 'object',
      required: ['authorization'],
      properties: {
        authorization: {
          type: 'string',
          pattern: '^Bearer '
        }
      }
    },
    body: {
      type: 'object',
      required: ['full_name'],
      properties: {
        full_name: {
          type: 'string',
          minLength: 2,
          maxLength: 255
        }
      }
    }
  },

  changePassword: {
    headers: {
      type: 'object',
      required: ['authorization'],
      properties: {
        authorization: {
          type: 'string',
          pattern: '^Bearer '
        }
      }
    },
    body: {
      type: 'object',
      required: ['current_password', 'new_password'],
      properties: {
        current_password: {
          type: 'string',
          minLength: 1
        },
        new_password: {
          type: 'string',
          minLength: 8,
          maxLength: 100
        }
      }
    }
  },

  listUsers: {
    headers: {
      type: 'object',
      required: ['authorization'],
      properties: {
        authorization: {
          type: 'string',
          pattern: '^Bearer '
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
        search: {
          type: 'string',
          maxLength: 255
        }
      }
    }
  },

  getUser: {
    headers: {
      type: 'object',
      required: ['authorization'],
      properties: {
        authorization: {
          type: 'string',
          pattern: '^Bearer '
        }
      }
    },
    params: {
      type: 'object',
      required: ['id'],
      properties: {
        id: {
          type: 'integer',
          minimum: 1
        }
      }
    }
  },

  updateUser: {
    headers: {
      type: 'object',
      required: ['authorization'],
      properties: {
        authorization: {
          type: 'string',
          pattern: '^Bearer '
        }
      }
    },
    params: {
      type: 'object',
      required: ['id'],
      properties: {
        id: {
          type: 'integer',
          minimum: 1
        }
      }
    },
    body: {
      type: 'object',
      properties: {
        full_name: {
          type: 'string',
          minLength: 2,
          maxLength: 255
        },
        is_active: {
          type: 'boolean'
        }
      }
    }
  },

  updateUserStatus: {
    headers: {
      type: 'object',
      required: ['authorization'],
      properties: {
        authorization: {
          type: 'string',
          pattern: '^Bearer '
        }
      }
    },
    params: {
      type: 'object',
      required: ['id'],
      properties: {
        id: {
          type: 'integer',
          minimum: 1
        }
      }
    },
    body: {
      type: 'object',
      required: ['is_active'],
      properties: {
        is_active: {
          type: 'boolean'
        }
      }
    }
  },

  assignRole: {
    headers: {
      type: 'object',
      required: ['authorization'],
      properties: {
        authorization: {
          type: 'string',
          pattern: '^Bearer '
        }
      }
    },
    params: {
      type: 'object',
      required: ['id'],
      properties: {
        id: {
          type: 'integer',
          minimum: 1
        }
      }
    },
    body: {
      type: 'object',
      required: ['role_id'],
      properties: {
        role_id: {
          type: 'integer',
          minimum: 1
        }
      }
    }
  },

  removeRole: {
    headers: {
      type: 'object',
      required: ['authorization'],
      properties: {
        authorization: {
          type: 'string',
          pattern: '^Bearer '
        }
      }
    },
    params: {
      type: 'object',
      required: ['id', 'roleId'],
      properties: {
        id: {
          type: 'integer',
          minimum: 1
        },
        roleId: {
          type: 'integer',
          minimum: 1
        }
      }
    }
  }
}

export default authValidation