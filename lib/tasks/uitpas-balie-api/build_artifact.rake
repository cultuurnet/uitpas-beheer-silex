namespace 'uitpas-balie-api' do
  desc "Create a debian package from the binaries."
  task :build_artifact do |task|

    calver_version = ENV['PIPELINE_VERSION'].nil? ? Time.now.strftime("%Y.%m.%d.%H%M%S") : ENV['PIPELINE_VERSION']
    git_short_ref  = `git rev-parse --short HEAD`.strip
    version        = ENV['ARTIFACT_VERSION'].nil? ? "#{calver_version}+sha.#{git_short_ref}" : ENV['ARTIFACT_VERSION']
    artifact_name  = 'uitpas-balie-api'
    vendor         = 'publiq VZW'
    maintainer     = 'Infra publiq <infra@publiq.be>'
    license        = 'Apache-2.0'
    description    = 'Silex backend for Balie UiTPAS'
    source         = 'https://github.com/cultuurnet/uitpas-beheer-angular'

    FileUtils.mkdir_p('pkg')
    FileUtils.cp('config.dist.yml','config.yml')

    system("fpm -s dir -t deb -n #{artifact_name} -v #{version} -a all -p pkg \
      -C dist -d 'php7.1-cli' \
      -x 'config.yml.dist' -x '.git*' -x pkg -x vendor -x lib -x Rakefile -x Gemfile -x Gemfile.lock \
      -x .bundle -x 'Jenkinsfile*' \
      --prefix /var/www/uitpas-balie-api \
      --config-files /var/www/uitpas-balie-api/config.yml \
      --deb-user www-data --deb-group www-data \
      --description '#{description}' --url '#{source}' --vendor '#{vendor}' \
      --license '#{license}' -m '#{maintainer}' \
      --deb-field 'Pipeline-Version: #{calver_version}' \
      --deb-field 'Git-Ref: #{git_short_ref}' \
      ."
    ) or exit 1
  end
end
