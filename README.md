#siminia

1. Clone pwa-studio

git clone https://github.com/magento-research/pwa-studio/
cd pwa-studio
cp packages/venia-concept/.env.dist packages/venia-concept/.env

2. Modify package.json

workspaces:

  "workspaces": [
...
    "packages/upward-spec",
    "packages/siminia"
  ],


scripts:
  "scripts": {
	...
    "watch:venia": "yarn workspace @magento/venia-concept run watch; cd - >/dev/null",
    "watch:siminia": "yarn workspace @simicart/siminia run watch; cd - >/dev/null",
    "stage:siminia": "yarn workspace @simicart/siminia run start; cd - >/dev/null"
  },

3. Clone siminia

cd  packages
git clone https://github.com/Simicart/siminia
cd ..
yarn install

4. Run watch/stage

yarn run watch:simipwa
yarn run build
NODE_ENV=production PORT=8080 npm run stage:siminia
