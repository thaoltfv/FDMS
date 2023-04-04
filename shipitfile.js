module.exports = shipit => {
  // Load shipit-deploy tasks
  require('shipit-deploy')(shipit)

  shipit.initConfig({
    default: {
      workspace: './dist',
      keepWorkspace: false,
      repositoryUrl: '', // Skip fetching repo
      ignores: ['.git', 'node_modules', 'deploy'],
      deleteOnRollback: true,
      shallowClone: false
    },
    develop: {
      deployTo: '/opt/docker/sources/donava/website_dev',
      servers: 'root@171.244.39.224',
      build: 'npm run build:dev',
    },
    production: {
      deployTo: '/opt/docker/sources/donava/website_staging',
      servers: 'root@171.244.39.224',
      build: 'npm run build:production',
    }
  })

  shipit.blTask('deploy:build', async () => {
    await shipit.local(shipit.config.build);
  })

  shipit.on('deploy', async () => {
    await shipit.start(['deploy:build']);
  })
}
