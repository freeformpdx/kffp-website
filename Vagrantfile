# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.network :private_network, ip: "192.168.50.50"

  config.vm.provision :ansible do |ansible|
    ansible.inventory_path = "ansible/vagrant-inventory"
    ansible.limit = "all"
    ansible.host_key_checking = "false"
    ansible.playbook = "ansible/playbook.yml"
    # uncomment below for visibility when Ansible playbook runs
    # ansible.verbose = "v"
  end

  config.vm.provider :virtualbox do |v|
    v.memory = 1024
    v.name = "kffpwebsite"
  end
  config.vm.synced_folder ".", "/vagrant/kffp-website", owner:"www-data", group:"www-data", mount_options:["dmode=775", "fmode=775"]
end
