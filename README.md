# FDMS - Fast Document Management System

🚀 **A modern, scalable document management system with unique table-per-blueprint architecture**

[![Status](https://img.shields.io/badge/Status-Complete-green.svg)](./IMPLEMENTATION_SUMMARY.md)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg)](./compose.yml)
[![Backend](https://img.shields.io/badge/Backend-Fastify-brightgreen.svg)](./backend/)
[![Frontend](https://img.shields.io/badge/Frontend-Vue3+Ionic-orange.svg)](./frontend/)
[![Database](https://img.shields.io/badge/Database-PostgreSQL+PostGIS-blue.svg)](./database/)

## 🌟 What Makes FDMS Unique

- **Table-per-Blueprint Architecture**: Each document type gets its own optimized PostgreSQL table
- **Zero-ETL Analytics**: Direct SQL access for business intelligence
- **Dynamic Schema Management**: Runtime table creation and safe migrations
- **Enterprise Security**: Multi-layer authentication and permissions
- **Mobile-First Design**: Vue 3 + Ionic responsive interface
- **Geospatial Support**: PostGIS integration for location-based documents

## ⚡ Quick Start

```bash
# 1. Clone and setup
git clone <your-repository>
cd fdms

# 2. Configure environment
cp .env.example .env

# 3. Start all services
docker compose up -d

# 4. Initialize database
docker compose exec backend npm run db:migrate

# 5. Access applications
# Frontend: http://localhost:5173
# Backend API: http://localhost:3000
# API Docs: http://localhost:3000/docs
```

## 🏗️ Architecture Overview

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Vue 3 + Ionic │────│  Fastify API    │────│  PostgreSQL +   │
│     Frontend    │    │     Backend     │    │     PostGIS     │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                              │
                       ┌─────────────────┐
                       │  Garage S3      │
                       │  File Storage   │
                       └─────────────────┘
```

## 🎯 Core Features

### 📋 Dynamic Document Types
- Create custom document blueprints
- 15+ field types (text, numbers, files, geospatial, etc.)
- Workflow stages and permissions
- Schema versioning and migrations

### 📄 Document Management
- CRUD operations on any document type
- Advanced search and filtering
- Document versioning and history
- Bulk operations and exports

### 📁 File Storage
- S3-compatible storage (Garage)
- Multiple file uploads
- Automatic validation and security
- File versioning and metadata

### 🔐 Security & Permissions
- JWT authentication
- Role-based access control
- Group permissions
- Activity audit logging

### 📊 Analytics & Reporting
- Real-time document statistics
- Export capabilities (CSV, JSON)
- Creation trends and analytics
- Storage usage monitoring

## 🛠️ Technology Stack

### Backend
- **Framework**: Fastify (Node.js)
- **Database**: PostgreSQL + PostGIS
- **Storage**: Garage (S3-compatible)
- **Auth**: JWT tokens
- **ORM**: Knex.js

### Frontend
- **Framework**: Vue 3 + TypeScript
- **UI**: Ionic Framework
- **State**: Pinia stores
- **Build**: Vite

### Infrastructure
- **Containers**: Docker Compose
- **Development**: Hot reload enabled
- **Documentation**: Auto-generated API docs

## 📚 Documentation

- **[Implementation Summary](./IMPLEMENTATION_SUMMARY.md)** - Complete technical overview
- **[API Documentation](http://localhost:3000/docs)** - Interactive API docs (when running)
- **[Database Schema](./database/schema.sql)** - Complete database structure
- **[Architecture Notes](./docs/ARCHITECTURE.md)** - System design details

## 🚀 Deployment

### Development
```bash
docker compose up -d
```

### Production
1. Update environment variables in `.env`
2. Configure SSL/TLS certificates
3. Set up backup strategies
4. Configure monitoring and logging

## 📱 Frontend Features

- **Mobile-First**: Responsive design for all devices
- **PWA Ready**: Progressive Web App capabilities
- **Real-time**: Live updates and notifications
- **Offline Support**: Work without internet connection

## 🔧 API Examples

### Create a Blueprint
```bash
curl -X POST http://localhost:3000/api/blueprints \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "invoice",
    "title": "Invoice Management",
    "description": "Invoice tracking system"
  }'
```

### Create a Document
```bash
curl -X POST http://localhost:3000/api/documents/invoice \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "invoice_number": "INV-2024-001",
    "amount": 1500.00,
    "due_date": "2024-03-15"
  }'
```

## 🧪 Testing

```bash
# Backend tests
cd backend
npm test

# Frontend tests
cd frontend
npm test

# Integration tests
npm run test:integration
```

## 📈 Performance

- **Database**: Optimized queries with proper indexing
- **Storage**: Efficient file handling with chunked uploads
- **Caching**: Redis-ready for production scaling
- **Monitoring**: Health checks and metrics

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

MIT License - see [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Issues**: [GitHub Issues](../../issues)
- **Documentation**: [Implementation Summary](./IMPLEMENTATION_SUMMARY.md)
- **API Reference**: http://localhost:3000/docs

## 🎉 Acknowledgments

Built with modern web technologies and following enterprise architecture patterns. Special thanks to the open-source community for the excellent tools and libraries.

---

**FDMS** - *Fast Document Management System*  
*Implementing tomorrow's document management today* 🚀
