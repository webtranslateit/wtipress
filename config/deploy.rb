set :application, "wtipress.webtranslateit.com"
set :deploy_to,   "/web/sites/#{application}"
set :repository,  "git@github.com:AtelierConvivialite/wtipress.git"
set :branch,      "gh-pages"
set :use_sudo,    false

set :scm, :git

role :web, "gimli.atelierconvivialite.com"
role :app, "gimli.atelierconvivialite.com"

after "deploy:restart", "deploy:cleanup"
