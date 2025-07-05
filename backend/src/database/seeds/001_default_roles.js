/**
 * @param { import("knex").Knex } knex
 * @returns { Promise<void> }
 */
exports.seed = async function(knex) {
  // Deletes ALL existing entries
  await knex('roles').del();
  
  // Inserts seed entries
  await knex('roles').insert([
    {
      code: 'super_admin',
      title: 'Super Administrator',
      description: 'Full system access, blueprint creation/deletion',
      is_system: true
    },
    {
      code: 'blueprint_admin',
      title: 'Blueprint Administrator',
      description: 'Blueprint design and management',
      is_system: true
    },
    {
      code: 'data_manager',
      title: 'Data Manager',
      description: 'Document CRUD within assigned blueprints',
      is_system: true
    },
    {
      code: 'viewer',
      title: 'Viewer',
      description: 'Read-only access to permitted documents',
      is_system: true
    },
    {
      code: 'auditor',
      title: 'Auditor',
      description: 'Access to activity logs and version history',
      is_system: true
    }
  ]);
}; 