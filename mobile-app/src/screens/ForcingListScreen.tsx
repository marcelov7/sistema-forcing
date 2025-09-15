import React, { useState } from 'react';
import {
  Box,
  VStack,
  HStack,
  Text,
  ScrollView,
  Input,
  Select,
  Button,
  useToast,
  FlatList,
  Pressable,
  Badge,
  Center,
  Spinner,
  IconButton,
  Menu,
} from 'native-base';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { useNavigation } from '@react-navigation/native';
import { forcingService } from '../services/forcingService';
import { Forcing, ForcingFilters } from '../types/Forcing';

interface ForcingCardProps {
  forcing: Forcing;
  onPress: () => void;
}

function ForcingCard({ forcing, onPress }: ForcingCardProps) {
  const getStatusColor = (status: string) => {
    switch (status) {
      case 'pendente': return 'warning';
      case 'liberado': return 'info';
      case 'forcado': return 'success';
      case 'solicitacao_retirada': return 'primary';
      case 'retirado': return 'secondary';
      default: return 'gray';
    }
  };

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'pendente': return 'time';
      case 'liberado': return 'checkmark-circle';
      case 'forcado': return 'warning';
      case 'solicitacao_retirada': return 'send';
      case 'retirado': return 'checkmark-done';
      default: return 'help';
    }
  };

  return (
    <Pressable onPress={onPress}>
      <Box
        bg="white"
        p={4}
        borderRadius="lg"
        shadow={1}
        mb={3}
        borderLeftWidth={4}
        borderLeftColor={`${getStatusColor(forcing.status)}.500`}
      >
        <VStack space={2}>
          {/* Header com TAG e Status */}
          <HStack justifyContent="space-between" alignItems="center">
            <Text fontSize="lg" fontWeight="bold" color="gray.800">
              {forcing.tag}
            </Text>
            <Badge colorScheme={getStatusColor(forcing.status)}>
              <HStack alignItems="center" space={1}>
                <Ionicons 
                  name={getStatusIcon(forcing.status)} 
                  size={12} 
                  color="white" 
                />
                <Text color="white" fontSize="xs">
                  {forcing.status_texto}
                </Text>
              </HStack>
            </Badge>
          </HStack>

          {/* Descrição */}
          <Text color="gray.600" numberOfLines={2}>
            {forcing.descricao_equipamento}
          </Text>

          {/* Info adicional */}
          <HStack justifyContent="space-between" alignItems="center">
            <VStack>
              <Text fontSize="sm" color="gray.500">
                Área: {forcing.area}
              </Text>
              <Text fontSize="sm" color="gray.500">
                Criado por: {forcing.user.name}
              </Text>
            </VStack>
            <Text fontSize="xs" color="gray.400">
              {forcing.data_forcing_formatada}
            </Text>
          </HStack>
        </VStack>
      </Box>
    </Pressable>
  );
}

export default function ForcingListScreen() {
  const navigation = useNavigation();
  const toast = useToast();
  const [filters, setFilters] = useState<ForcingFilters>({});
  const [showFilters, setShowFilters] = useState(false);

  const { data, isLoading, error, refetch } = useQuery({
    queryKey: ['forcings', filters],
    queryFn: () => forcingService.getForcings(filters),
    onError: (error: any) => {
      toast.show({
        title: 'Erro ao carregar forcings',
        description: error.message,
        status: 'error',
      });
    },
  });

  const handleForcingPress = (forcing: Forcing) => {
    navigation.navigate('ForcingDetail', { forcing });
  };

  const clearFilters = () => {
    setFilters({});
  };

  if (isLoading) {
    return (
      <Center flex={1}>
        <Spinner size="large" color="blue.500" />
        <Text mt={4} color="gray.600">
          Carregando forcings...
        </Text>
      </Center>
    );
  }

  return (
    <Box flex={1} bg="gray.50" safeArea>
      <VStack flex={1}>
        {/* Header */}
        <Box bg="white" px={4} py={3} shadow={1}>
          <HStack justifyContent="space-between" alignItems="center">
            <Text fontSize="xl" fontWeight="bold" color="gray.800">
              Forcings
            </Text>
            <HStack space={2}>
              <IconButton
                icon={<Ionicons name="filter" size={20} color="gray" />}
                onPress={() => setShowFilters(!showFilters)}
                variant="ghost"
              />
              <IconButton
                icon={<Ionicons name="add" size={20} color="blue" />}
                onPress={() => navigation.navigate('ForcingCreate')}
                variant="ghost"
              />
            </HStack>
          </HStack>
        </Box>

        {/* Filtros */}
        {showFilters && (
          <Box bg="white" p={4} shadow={1}>
            <VStack space={3}>
              <Text fontSize="md" fontWeight="semibold" color="gray.800">
                Filtros
              </Text>
              
              <Input
                placeholder="Buscar por TAG ou descrição..."
                value={filters.busca || ''}
                onChangeText={(text) => setFilters({ ...filters, busca: text })}
                InputLeftElement={
                  <Ionicons name="search" size={20} color="gray" style={{ marginLeft: 12 }} />
                }
              />

              <HStack space={3}>
                <Select
                  placeholder="Status"
                  selectedValue={filters.status}
                  onValueChange={(value) => setFilters({ ...filters, status: value })}
                  flex={1}
                >
                  <Select.Item label="Todos" value="" />
                  <Select.Item label="Pendente" value="pendente" />
                  <Select.Item label="Liberado" value="liberado" />
                  <Select.Item label="Forçado" value="forcado" />
                  <Select.Item label="Solic. Retirada" value="solicitacao_retirada" />
                  <Select.Item label="Retirado" value="retirado" />
                </Select>

                <Select
                  placeholder="Área"
                  selectedValue={filters.area}
                  onValueChange={(value) => setFilters({ ...filters, area: value })}
                  flex={1}
                >
                  <Select.Item label="Todas" value="" />
                  {/* Adicionar áreas dinamicamente */}
                </Select>
              </HStack>

              <HStack space={3}>
                <Button
                  size="sm"
                  variant="outline"
                  onPress={clearFilters}
                  flex={1}
                >
                  Limpar
                </Button>
                <Button
                  size="sm"
                  onPress={() => setShowFilters(false)}
                  flex={1}
                >
                  Aplicar
                </Button>
              </HStack>
            </VStack>
          </Box>
        )}

        {/* Lista */}
        {data && data.data.length > 0 ? (
          <FlatList
            data={data.data}
            renderItem={({ item }) => (
              <ForcingCard
                forcing={item}
                onPress={() => handleForcingPress(item)}
              />
            )}
            keyExtractor={(item) => item.id.toString()}
            px={4}
            py={4}
            refreshing={isLoading}
            onRefresh={refetch}
          />
        ) : (
          <Center flex={1}>
            <Ionicons name="document-outline" size={64} color="gray" />
            <Text mt={4} color="gray.600" textAlign="center">
              Nenhum forcing encontrado
            </Text>
            <Button
              mt={4}
              onPress={() => navigation.navigate('ForcingCreate')}
              leftIcon={<Ionicons name="add" size={20} color="white" />}
            >
              Criar Primeiro Forcing
            </Button>
          </Center>
        )}
      </VStack>
    </Box>
  );
}

