require 'date'
require 'nokogiri'
require 'digest/md5'
require 'fileutils'
require 'json'

class String
  def underscore
    self.gsub(/::/, '/').
    gsub(/([A-Z]+)([A-Z][a-z])/,'\1_\2').
    gsub(/([a-z\d])([A-Z])/,'\1_\2').
    tr("-", "_").
    downcase
  end

  def unindent
    gsub(/^#{self[/\A\s*/]}/, '')
  end
end

task :phar, :version do |t, args|
  version = args[:version]

  File.open("stub.php", "w") do |f|
    f.write(<<-STUB.unindent)
    <?php
    Phar::mapPhar();

    $basePath = 'phar://' . __FILE__ . '/';

    spl_autoload_register(function($class) use ($basePath)
    {
        if (0 !== strpos($class, "Imagine\\\\")) {
            return false;
        }
        $path = str_replace('\\\\', DIRECTORY_SEPARATOR, substr($class, 8));
        $file = $basePath.$path.'.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    });

    __HALT_COMPILER();
    STUB
  end

  system "phar-build -s #{Dir.pwd}/lib/Imagine -S #{Dir.pwd}/stub.php --phar #{Dir.pwd}/imagine.phar --ns"

  File.unlink("stub.php")
end

task :test do
  system "phpunit tests/"
end

task :sphinx do
  `git ls-files lib/Imagine*.php`.split("\n").each do |f|
    rst_file = f.gsub(/^lib\/Imagine(.*)\.php/) { |s| "docs/api#{$1}.rst" }.underscore
    rst_dir  = File.dirname(rst_file)
    FileUtils.mkdir_p(rst_dir) unless Dir.exists?(rst_dir)
    system "doxphp < #{f} | doxphp2sphinx > #{rst_file}"
  end
end

task :clean do
  system "git clean -df"
end

task :pear, :version do |t, args|
  Dir.chdir("lib")
  version = args[:version]
  now     = DateTime.now
  hash    = Digest::MD5.new
  xml     = Nokogiri::XML::Builder.new do |xml|
    xml.package(:packagerversion => "1.8.0", :version => "2.0",
                :xmlns => "http://pear.php.net/dtd/package-2.0",
                "xmlns:tasks" => "http://pear.php.net/dtd/tasks-1.0",
                "xmlns:xsi" => "http://www.w3.org/2001/XMLSchema-instance",
                "xsi:schemaLocation" => [
                  "http://pear.php.net/dtd/tasks-1.0",
                  "http://pear.php.net/dtd/tasks-1.0.xsd",
                  "http://pear.php.net/dtd/package-2.0",
                  "http://pear.php.net/dtd/package-2.0.xsd"
                ].join(" ")) {
      xml.name "Imagine"
      xml.channel "pear.avalanche123.com"
      xml.summary "PHP 5.3 Object Oriented image manipulation library."
      xml.description "Image manipulation library for PHP 5.3 inspired by Python's PIL and other image libraries."
      xml.lead {
        xml.name   "Bulat Shakirzyanov"
        xml.user   "avalanche123"
        xml.email  "mallluhuct at gmail.com"
        xml.active "yes"
      }
      xml.date now.strftime('%Y-%m-%d')
      xml.time now.strftime('%H:%M:%S')
      xml.version {
        xml.release version
        xml.api     version
      }
      xml.stability {
        xml.release "beta"
        xml.api     "beta"
      }
      xml.license "MIT", :uri => "http://www.opensource.org/licenses/mit-license.php"
      xml.notes "-"
      xml.contents {
        xml.dir(:name => "/") {
          `git ls-files`.split("\n").each { |f|
            open(f, "r") do |io|
              while (!io.eof)
                hash.update(io.readpartial(1024))
              end
            end
            xml.file(:md5sum => hash.hexdigest, :role => "php", :name => f)
          }
        }
      }
      xml.dependencies {
        xml.required {
          xml.php {
            xml.min "5.3.2"
          }
          xml.pearinstaller {
            xml.min "1.4.0"
          }
        }
      }
      xml.phprelease
    }
  end
  File.open("package.xml", "w") { |f| f.write(xml.to_xml) }
  system "pear package"
  File.unlink("package.xml")
  FileUtils.mv("Imagine-#{version}.tgz", "../")
end

task :composer, :version do |t, args|
  version = args[:version]
  File.open("composer.json", "w") do |f|
    f.write(JSON.pretty_generate(
      "name" => "imagine/Imagine",
      "description" => "Image processing for PHP 5.3",
      "keywords" => ["image manipulation","image processing", "drawing", "graphics"],
      "homepage" => "http://imagine.readthedocs.org/",
      "license" => "MIT",
      "authors" => [
        {
          "name" => "Bulat Shakirzyanov",
          "email" => "mallluhuct@gmail.com",
          "homepage" => "http://avalanche123.com"
        }
      ],
      "require" => {
        "php" => ">=5.3.2"
      },
      "autoload" => {
        "psr-0" => { "Imagine" => "lib/" }
      }
    ))
  end
end

task :release, :version do |t, args|
  version = args[:version]

  Rake::Task["test"]

  Rake::Task["sphinx"].invoke

  system "git add docs/api"
  system "git commit -m \"updated api docs for release #{version}\""

  Rake::Task["composer"].invoke(version)

  system "git add composer.json"
  system "git commit -m \"updated composer.json for #{version} release\""

  Rake::Task["pear"].invoke(version)
  Rake::Task["phar"].invoke(version)

  system "git add imagine.phar"
  system "git commit -m \"update phar distribution for #{version}\""

  system "git checkout master"
  system "git merge develop"
  system "git tag v#{version} -m \"release v#{version}\""
  system "git push"
  system "git push --tags"
end
