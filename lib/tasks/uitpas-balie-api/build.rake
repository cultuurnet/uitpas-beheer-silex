namespace 'uitpas-balie-api' do
  desc "Build binaries"
  task :build do |task|

    system('composer install --no-dev --ignore-platform-reqs --optimize-autoloader --no-interaction') or exit 1

  end
end
