# -*- coding: utf-8 -*-

import sys
import os
import redis
import base64
import inspect

# 先安装扩展库 redis
# pip install redis


class Redis:

    def __init__(self, host='127.0.0.1', db=10):
        # 连接redis
        self.r = redis.Redis(host=host, port=6379, db=db)

    def __del__(self):
        print('销毁')

    def download_imgs(self, save_path, hase_name='test_imgs'):
        try:
            num = 0
            for i in self.r.sscan_iter(hase_name):
                hase_key = i.decode()
                file_name = save_path + hase_key
                current_save_path = os.path.dirname(file_name)
                # print(file_name)
                # print(current_save_path)
                # exit()

                if not os.path.exists(current_save_path):
                    os.makedirs(current_save_path)

                with open(file_name, 'wb') as f:
                    print(num, file_name)
                    img_base64 = self.r.get(hase_key)
                    data = base64.b64decode(img_base64)
                    f.write(data)

                    self.r.delete(hase_key)
                    self.r.srem(hase_name, hase_key)

                num += 1

        except Exception as e:
            exit(e)


if __name__ == '__main__':

    r = Redis('127.0.0.1')

    # base64转图片存文件
    r.download_imgs('e:/www/nfsqcx/public/common/clock_in_and_out_pics/', 'clock_in_and_out_pics')


    print('操作结束')
