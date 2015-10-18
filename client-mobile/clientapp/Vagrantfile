# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box = "ubuntu/trusty64"
  config.vm.box_check_update = true

  config.vm.boot_timeout = 500

  config.vm.synced_folder "./", "/home/vagrant/clientapp"

  config.vm.network "forwarded_port", guest: 5115, host: 5115
  config.vm.network "forwarded_port", guest: 5116, host: 5116
  config.vm.network "forwarded_port", guest: 35729, host: 35729
  config.vm.network "forwarded_port", guest: 9876, host: 9876

  config.vm.provision :shell, inline: "apt-get update", privileged: true
  config.vm.provision :shell, inline: "apt-get install -y build-essential libssl-dev git", privileged: true
  config.vm.provision :shell, inline: "apt-get install -y xvfb gtk2-engines-pixbuf xfonts-cyrillic xfonts-100dpi xfonts-75dpi xfonts-base xfonts-scalable", privileged: true

  config.vm.provision :shell, path: "provision.sh", privileged: false

  config.vm.provider :virtualbox do |vb|
    vb.customize ["modifyvm", :id, "--name", "Eardish Client Dev Env"]
    vb.customize ["modifyvm", :id, "--memory", "2048"]
    vb.customize ["modifyvm", :id, "--cpus", "2"]
    vb.customize ["modifyvm", :id, "--ioapic", "on"]
  end

  # On every vagrant up
  config.vm.provision :shell, privileged: false, run: "always", path: "start.sh"

end
