[![Contributors](https://img.shields.io/github/contributors/pixelwhiz/Gokart)](https://github.com/pixelwhiz/GoKart/graphs/contributors)
[![Forks](https://img.shields.io/github/forks/pixelwhiz/Gokart)](https://github.com/pixelwhiz/GoKart/network/members)
[![Stargazers](https://img.shields.io/github/stars/pixelwhiz/GoKart)](https://github.com/pixelwhiz/GoKart/stargazers)
[![Issues](https://img.shields.io/github/issues/pixelwhiz/GoKart)](https://github.com/pixelwhiz/GoKart/issues)
[![License](https://img.shields.io/github/license/pixelwhiz/GoKart)](https://github.com/pixelwhiz/GoKart/blob/master/LICENSE)

# 🏎️ GoKart Plugin

GoKart is a Minecraft plugin that allows players to ride and control custom minecarts with energy management and refueling mechanics. It integrates with the EconomyAPI for handling in-game currency transactions when refilling the minecart's energy.

# 🎉 Features

- Ride and control minecarts.
- Manage minecart energy levels.
- Refuel minecarts at gas stations using in-game currency.
- Handle minecart behavior with realistic physics, including jumping and movement adjustments.

# ⚙️ Installation

1. **Download the Plugin:**
   - Download the latest release from the [releases page](#).

2. **Place the Plugin:**
   - Move the `GoKart` plugin `.phar` file into the `plugins` directory of your Minecraft server.

3. **Restart the Server:**
   - Restart your server to load the plugin.

# ⚙️ Configuration

The plugin does not require any additional configuration out of the box. However, you can customize certain aspects of the plugin through the provided classes in the codebase.

# 📣 Commands

- **/gokart**
  - Opens the GoKart menu where players can interact with minecarts.

# 🚴‍♂️ Interacting with Minecarts

1. **Ride a Minecart:**
   - Use a Minecart item from your inventory to mount and ride it.

2. **Refuel Minecart:**
   - Use the command `/gokart recharge` to recharge your minecart entity.

# 🗒️ API Reference

## `Controller` Class

- **`shouldJump(Minecart $entity): array`**
  - Determines if the minecart should jump based on the current block and energy.

- **`shouldDrop(Minecart $entity)`**
  - Checks if the minecart should drop based on its current position (e.g., falling into water).

- **`shouldDespawn(Minecart $entity)`**
  - Determines if the minecart should despawn if it falls into lava.

- **`getMotion(Minecart $entity): array`**
  - Calculates the motion vector for the minecart based on the rider's direction and energy levels.

- **`refillEnergy(Minecart $entity, int $amount, int $price)`**
  - Refills the minecart's energy, adjusting the player’s balance accordingly.

- **`updateEnergy(Minecart $entity, array $startPos): bool`**
  - Updates the minecart's energy based on its movement from the start position.

## `GasStation` Class

- **`open(Player $player, Minecart $entity): CustomForm`**
  - Opens the custom form for refueling the minecart.

- **`confirm(Player $player, int $energy, int $amount): ModalForm`**
  - Shows a confirmation form for refueling the minecart with the specified amount of energy.

## `RandomUtils` Class

- **`createBaseNBT(Vector3 $pos, ?Vector3 $motion = null, float $yaw = 0.0, float $pitch = 0.0): CompoundTag`**
  - Creates the base NBT tag for a minecart entity with the given position, motion, and rotation.

- **`setEnergyNBT(Minecart $entity): CompoundTag`**
  - Sets the energy NBT tag for a minecart entity.

# 👥 Contributing

Contributions are welcome! If you have suggestions or improvements, feel free to open an issue or submit a [Pull Request](https://github.com/pixelwhiz/GokartPro/compare) on this repository.

# ⚠️ Reporting Bugs

If you encounter any bugs or issues, please report them on the [Issue](https://github.com/pixelwhiz/GokartPro/issues/new). Provide a detailed description of the issue, steps to reproduce, and any relevant logs.

# 🪪 License

This plugin is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

# ✉️ Contact

For any questions or support, please contact me on [Discord](https://discordapp.com/users/591983759965028363).
