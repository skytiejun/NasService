#!/bin/bash

script_dir=/home/www	# ssh-authkey.sh ��ũ��Ʈ������ġ

#local_user=nobody
local_user=root


#root�������� �ٸ������� ����Ű ���鶧�� �Ʒ� ó�� �۹̼��� �����������( �Ʒ� ����  kvm �����ϰ��)
#chmod 600 /home/kvm/.ssh/authorized_keys
#chmod 700 /home/kvm/.ssh


remote_user=root
#remote_host=localhost
#remote_host=192.168.2.152
remote_host="localhost"
remote_port=5135
#remote_home=/smilemrtg
target=${remote_user}@${remote_host}

saveas=./key
logdir=./key/logs	#log ���� ���丮
Identity=$saveas/id_rsa
KnownHostsFile=$saveas/known_hosts

if [ ! -d $script_dir ] ; then
	echo ""
	echo "${script_dir} �� �������� �ʽ��ϴ�."
	echo "ssh-authkey.sh ������ġ�� �ٽ� Ȯ���ϱ� �ٶ��ϴ�."
	echo ""
	exit
fi

cd $script_dir

case "$1" in
        install)

			if [ ! -d $saveas ] ; then
				mkdir -p $saveas
				chmod 707 $saveas

				mkdir -p $logdir
				chmod 755 $logdir
				chown nobody:nobody $logdir

			fi				


			if [ ! -f $saveas/id_rsa ] ; then
        	#ssh-keygen -f id_rsa  -N "" -t rsa

				sudo -u $local_user  ssh-keygen -t rsa -f $Identity
#				chown $local_user $Identity $Identity.pub     #2010. 8. 25 ������ �߰�
				chown nobody:root $saveas/*     #2010. 8. 25 ������ �߰�, nobody������ �ο��ؾ��� ������ ��ɾ� �����

			fi

#			local_user_home=$(grep ^${local_user}: /etc/passwd | awk -F: '{print $6}')	#Ȯ���� ���� ���⼭ ���ʵǰ� ����. 2011.3.14 ������
# ���� ���丮 �̾Ƴ��� ��ũ��Ʈ�� ��������. root, root1, root2 �̷��� �ٰ������� �Ǿ�����.... ����������
#			if [ ! -d ${local_user_home}/.ssh ] ; then
#			    mkdir -p ${local_user_home}/.ssh	#���ֵ� �ɵ�... 2011.3.14 ������
#			fi
			
			if [ ! -d /.ssh ] ; then
			    mkdir -p /.ssh	# /.ssh ���丮�� ������ cmd�� ������ �ʵ�(������ Ȯ���غ�����)
			fi



			cat $Identity.pub | ssh -i $Identity -o UserKnownHostsFile=$KnownHostsFile  -p $remote_port ${target} 'cat >> .ssh/authorized_keys'

#			scp -i $Identity -o UserKnownHostsFile=$KnownHostsFile  id_rsa.pub ${remote_user}@${remote_host}:${remote_home}/.ssh/authorized_keys
#			scp -i $Identity -o UserKnownHostsFile=$KnownHostsFile  ../key/id_rsa.pub ${target}:${remote_home}/.ssh/authorized_keys	#������ cat��ɾ�� �ߺ��Ǵµ�..2011.3.14 ������

        ;;


		alive)
			ssh  -i $Identity -o UserKnownHostsFile=$KnownHostsFile -o PasswordAuthentication=no  -p $remote_port ${target} 'echo alive'
		;;


		scp)
#			dst=${target}:$dst
#			scp -i $Identity -o UserKnownHostsFile=$KnownHostsFile  $src $dst
		;;


		exec)
			ssh -i $Identity -o UserKnownHostsFile=$KnownHostsFile  $target
		;;


		expire)
#			ssh -i $Identity -o UserKnownHostsFile=$KnownHostsFile    ${target} rm -f ${remote_home}/.ssh/authorized_keys
		;;


		help)
			echo "$0 [ install | alive | scp | exec | expire ]"
		;;


		clean)
			rm -f $Identity $Identity.pub
		;;
esac

function get_user_home()
{
        case "$1" in
        root)
        remote_home=/root
        ;;
        *)
        remote_home=/home/${remote_user}
        ;;
        esac
}
