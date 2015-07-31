Vagrant.configure("2") do |config|
      config.vm.box = "precise64"
      config.vm.box_url = "http://files.vagrantup.com/precise64.box"
      config.vm.network :forwarded_port, guest: 80, host: 8081
      config.vm.network :forwarded_port, guest: 3306, host: 3307
      config.vm.provider "virtualbox" do |vb|
        vb.customize ["modifyvm", :id, "--memory", 1024]
      end
      # config.ssh.max_tries = 50
      # config.ssh.timeout   = 300
      config.vm.synced_folder ".", "/vagrant", :id => "vagrant-root", :owner => "vagrant", :group => "www-data"
end